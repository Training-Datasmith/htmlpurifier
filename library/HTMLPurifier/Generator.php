<?php

declare(strict_types=1);

/**
 * Generates HTML from tokens.
 * @todo Refactor interface so that configuration/context is determined
 *       upon instantiation, no need for messy generateFromTokens() calls
 * @todo Make some of the more internal functions protected, and have
 *       unit tests work around that
 */
class HTMLPurifier_Generator
{
    /**
     * Whether or not generator should produce XML output.
     * @type bool
     */
    private $_xhtml = true;

    /**
     * :HACK: Whether or not generator should comment the insides of <script> tags.
     * @type bool
     */
    private $_scriptFix = false;

    /**
     * Cache of HTMLDefinition during HTML output to determine whether or
     * not attributes should be minimized.
     * @type HTMLPurifier_HTMLDefinition
     */
    private $_def;

    /**
     * Cache of %Output.SortAttr.
     * @type bool
     */
    private $_sortAttr;

    /**
     * Cache of %Output.FlashCompat.
     * @type bool
     */
    private $_flashCompat;

    /**
     * Cache of %Output.FixInnerHTML.
     * @type bool
     */
    private $_innerHTMLFix;

    /**
     * Stack for keeping track of object information when outputting IE
     * compatibility code.
     * @type array
     */
    private $_flashStack = [];

    /**
     * Configuration for the generator
     * @type HTMLPurifier_Config
     */
    protected $config;

    /**
     * @param HTMLPurifier_Config $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->_scriptFix = $config->get('Output.CommentScriptContents');
        $this->_innerHTMLFix = $config->get('Output.FixInnerHTML');
        $this->_sortAttr = $config->get('Output.SortAttr');
        $this->_flashCompat = $config->get('Output.FlashCompat');
        $this->_def = $config->getHTMLDefinition();
        $this->_xhtml = $this->_def->doctype->xml;
    }

    /**
     * Generates an HTML string from an array of clean tokens.
     *
     * This method is called after all purification strategies have run.  It
     * serialises the token stream back to an HTML string, handling XHTML
     * self-closing syntax, attribute quoting, and optional Tidy formatting.
     *
     * @security This method must ONLY be called with tokens that have already
     *           been through the full purification pipeline (RemoveForeignElements,
     *           MakeWellFormed, FixNesting, ValidateAttributes).  Passing raw,
     *           unpurified tokens will produce unsafe output.
     *
     * @security Attribute values are HTML-entity-encoded by generateFromToken()
     *           via htmlspecialchars().  Do not bypass this step.
     *
     * @complexity O(n) in the number of tokens; the optional Tidy pass adds
     *             an O(n log n) formatting step if Output.TidyFormat is enabled.
     *
     * @param HTMLPurifier_Token[] $tokens  The purified token stream.
     *
     * @return string The serialised HTML string.  Safe to insert into a page
     *                provided the surrounding context is also safe.
     */
    public function generateFromTokens(array $tokens)
    {
        if (!$tokens) {
            return '';
        }

        // Basic algorithm
        $html = '';
        for ($i = 0, $size = count($tokens); $i < $size; $i++) {
            if ($this->_scriptFix && $tokens[$i]->name === 'script'
                && $i + 2 < $size && $tokens[$i + 2] instanceof HTMLPurifier_Token_End) {
                // script special case
                // the contents of the script block must be ONE token
                // for this to work.
                $html .= $this->generateFromToken($tokens[$i++]);
                $html .= $this->generateScriptFromToken($tokens[$i++]);
            }
            $html .= $this->generateFromToken($tokens[$i]);
        }

        // Tidy cleanup
        if (extension_loaded('tidy') && $this->config->get('Output.TidyFormat')) {
            $tidy = new Tidy();
            $tidy->parseString(
                $html,
                [
                   'indent' => true,
                   'output-xhtml' => $this->_xhtml,
                   'show-body-only' => true,
                   'indent-spaces' => 2,
                   'wrap' => 68,
                ],
                'utf8'
            );
            $tidy->cleanRepair();
            $html = (string) $tidy; // explicit cast necessary
        }

        // Normalize newlines to system defined value
        if ($this->config->get('Core.NormalizeNewlines')) {
            $nl = $this->config->get('Output.Newline');
            if ($nl === null) {
                $nl = PHP_EOL;
            }
            if ($nl !== "\n") {
                $html = str_replace("\n", $nl, $html);
            }
        }
        return $html;
    }

    /**
     * Generates an HTML fragment from a single token.
     *
     * Handles start tags, end tags, empty (self-closing) tags, text nodes, and
     * comment tokens.  Attribute values are HTML-entity-encoded with
     * htmlspecialchars(ENT_COMPAT, 'UTF-8') to prevent attribute injection.
     *
     * @security Text tokens are NOT re-escaped here because they should already
     *           have been converted to safe content by the purification pipeline.
     *           If you add tokens to the stream manually (outside the pipeline)
     *           you MUST ensure they do not contain unescaped HTML.
     *
     * @param HTMLPurifier_Token $token  A single purified token.
     *
     * @return string The HTML string representation of the token.
     */
    public function generateFromToken($token)
    {
        if (!$token instanceof HTMLPurifier_Token) {
            trigger_error('Cannot generate HTML from non-HTMLPurifier_Token object', E_USER_WARNING);
            return '';
        }
        if ($token instanceof HTMLPurifier_Token_Start) {
            $attr = $this->generateAttributes($token->attr, $token->name);
            if ($this->_flashCompat) {
                if ($token->name == 'object') {
                    $flash = new stdClass();
                    $flash->attr = $token->attr;
                    $flash->param = [];
                    $this->_flashStack[] = $flash;
                }
            }
            return '<' . $token->name . ($attr ? ' ' : '') . $attr . '>';
        }
        if ($token instanceof HTMLPurifier_Token_End) {
            $_extra = '';
            if ($this->_flashCompat) {
                if ($token->name == 'object' && !empty($this->_flashStack)) {
                    // doesn't do anything for now
                }
            }
            return $_extra . '</' . $token->name . '>';
        }
        if ($token instanceof HTMLPurifier_Token_Empty) {
            if ($this->_flashCompat && $token->name == 'param' && !empty($this->_flashStack)) {
                $this->_flashStack[count($this->_flashStack) - 1]->param[$token->attr['name']] = $token->attr['value'];
            }
            $attr = $this->generateAttributes($token->attr, $token->name);
            return '<' . $token->name . ($attr ? ' ' : '') . $attr .
               ($this->_xhtml ? ' /' : '') // <br /> v. <br>
               . '>';
        }
        if ($token instanceof HTMLPurifier_Token_Text) {
            return $this->escape($token->data, ENT_NOQUOTES);
        }
        if ($token instanceof HTMLPurifier_Token_Comment) {
            return '<!--' . $token->data . '-->';
        }
        return '';
    }

    /**
     * Special case processor for the contents of script tags
     * @param HTMLPurifier_Token $token HTMLPurifier_Token object.
     * @return string
     * @warning This runs into problems if there's already a literal
     *          --> somewhere inside the script contents.
     */
    public function generateScriptFromToken($token)
    {
        if (!$token instanceof HTMLPurifier_Token_Text) {
            return $this->generateFromToken($token);
        }
        // Thanks <http://lachy.id.au/log/2005/05/script-comments>
        $data = preg_replace('#//\s*$#', '', $token->data);
        return '<!--//--><![CDATA[//><!--' . "\n" . trim($data) . "\n" . '//--><!]]>';
    }

    /**
     * Generates attribute declarations from attribute array.
     * @note This does not include the leading or trailing space.
     * @param array $assoc_array_of_attributes Attribute array
     * @param string $element Name of element attributes are for, used to check
     *        attribute minimization.
     * @return string Generated HTML fragment for insertion.
     */
    public function generateAttributes($assoc_array_of_attributes, $element = '')
    {
        $html = '';
        if ($this->_sortAttr) {
            ksort($assoc_array_of_attributes);
        }
        foreach ($assoc_array_of_attributes as $key => $value) {
            if (!$this->_xhtml) {
                // Remove namespaced attributes
                if (strpos($key, ':') !== false) {
                    continue;
                }
                // Check if we should minimize the attribute: val="val" -> val
                if ($element && !empty($this->_def->info[$element]->attr[$key]->minimized)) {
                    $html .= $key . ' ';
                    continue;
                }
            }
            // Workaround for Internet Explorer innerHTML bug.
            // Essentially, Internet Explorer, when calculating
            // innerHTML, omits quotes if there are no instances of
            // angled brackets, quotes or spaces.  However, when parsing
            // HTML (for example, when you assign to innerHTML), it
            // treats backticks as quotes.  Thus,
            //      <img alt="``" />
            // becomes
            //      <img alt=`` />
            // becomes
            //      <img alt='' />
            // Fortunately, all we need to do is trigger an appropriate
            // quoting style, which we do by adding an extra space.
            // This also is consistent with the W3C spec, which states
            // that user agents may ignore leading or trailing
            // whitespace (in fact, most don't, at least for attributes
            // like alt, but an extra space at the end is barely
            // noticeable).  Still, we have a configuration knob for
            // this, since this transformation is not necessary if you
            // don't process user input with innerHTML or you don't plan
            // on supporting Internet Explorer.
            if ($this->_innerHTMLFix) {
                if (strpos($value, '`') !== false) {
                    // check if correct quoting style would not already be
                    // triggered
                    if (strcspn($value, '"\' <>') === strlen($value)) {
                        // protect!
                        $value .= ' ';
                    }
                }
            }
            $html .= $key.'="'.$this->escape($value).'" ';
        }
        return rtrim($html);
    }

    /**
     * Escapes raw text data.
     * @todo This really ought to be protected, but until we have a facility
     *       for properly generating HTML here w/o using tokens, it stays
     *       public.
     * @param string $string String data to escape for HTML.
     * @param int $quote Quoting style, like htmlspecialchars. ENT_NOQUOTES is
     *               permissible for non-attribute output.
     * @return string escaped data.
     */
    public function escape($string, $quote = null)
    {
        // Workaround for APC bug on Mac Leopard reported by sidepodcast
        // http://htmlpurifier.org/phorum/read.php?3,4823,4846
        if ($quote === null) {
            $quote = ENT_COMPAT;
        }
        return htmlspecialchars($string, $quote, 'UTF-8');
    }
}

// vim: et sw=4 sts=4
