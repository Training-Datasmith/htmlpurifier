<?php

declare (strict_types=1);
/**
 * Takes the contents of blockquote when in strict and reformats for validation.
 */
class Html_Purifier_child_Def_strict_Blockquote extends Html_Purifier_child_Def_required
{
    /**
     * @type array
     */
    protected $real_elements;
    /**
     * @type array
     */
    protected $fake_elements;
    /**
     * @type bool
     */
    public $allow_empty = true;
    /**
     * @type string
     */
    public $type = 'strictblockquote';
    /**
     * @type bool
     */
    protected $init = false;
    /**
     * @param HTMLPurifier_Config $config
     * @return array
     * @note We don't want MakeWellFormed to auto-close inline elements since
     *       they might be allowed.
     */
    public function get_allowed_elements($config)
    {
        $this->init($config);
        return $this->fake_elements;
    }
    /**
     * @param array $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function validate_children($children, $config, $context)
    {
        $this->init($config);
        // trick the parent class into thinking it allows more
        $this->elements = $this->fake_elements;
        $result = parent::validate_children($children, $config, $context);
        $this->elements = $this->real_elements;
        if ($result === false) {
            return [];
        }
        if ($result === true) {
            $result = $children;
        }
        $def = $config->get_html_definition();
        $block_wrap = false;
        $ret = [];
        foreach ($result as $node) {
            if ($block_wrap === false) {
                if ($node instanceof Html_Purifier_node_text && !$node->is_whitespace || $node instanceof Html_Purifier_node_element && !isset($this->elements[$node->name])) {
                    $block_wrap = new Html_Purifier_node_element($def->info_block_wrapper);
                    $ret[] = $block_wrap;
                }
            } else if ($node instanceof Html_Purifier_node_element && isset($this->elements[$node->name])) {
                $block_wrap = false;
            }
            if ($block_wrap) {
                $block_wrap->children[] = $node;
            } else {
                $ret[] = $node;
            }
        }
        return $ret;
    }
    /**
     * @param HTMLPurifier_Config $config
     */
    private function init($config)
    {
        if (!$this->init) {
            $def = $config->get_html_definition();
            // allow all inline elements
            $this->real_elements = $this->elements;
            $this->fake_elements = $def->info_content_sets['Flow'];
            $this->fake_elements['#PCDATA'] = true;
            $this->init = true;
        }
    }
}
// vim: et sw=4 sts=4