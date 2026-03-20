<?php

declare (strict_types=1);
/**
 * Defines allowed CSS attributes and what their values are.
 * @see HTMLPurifier_HTMLDefinition
 */
class Html_Purifier_css_Definition extends Html_Purifier_definition
{
    public $type = 'CSS';
    /**
     * Assoc array of attribute name to definition object.
     * @type HTMLPurifier_AttrDef[]
     */
    public $info = [];
    /**
     * Constructs the info array.  The meat of this class.
     * @param HTMLPurifier_Config $config
     */
    protected function do_setup($config)
    {
        $this->info['text-align'] = new Html_Purifier_attr_Def_enum(['left', 'right', 'center', 'justify'], false);
        $this->info['direction'] = new Html_Purifier_attr_Def_enum(['ltr', 'rtl'], false);
        $border_style = $this->info['border-bottom-style'] = $this->info['border-right-style'] = $this->info['border-left-style'] = $this->info['border-top-style'] = new Html_Purifier_attr_Def_enum(['none', 'hidden', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset'], false);
        $this->info['border-style'] = new Html_Purifier_attr_Def_css_multiple($border_style);
        $this->info['clear'] = new Html_Purifier_attr_Def_enum(['none', 'left', 'right', 'both'], false);
        $this->info['float'] = new Html_Purifier_attr_Def_enum(['none', 'left', 'right'], false);
        $this->info['font-style'] = new Html_Purifier_attr_Def_enum(['normal', 'italic', 'oblique'], false);
        $this->info['font-variant'] = new Html_Purifier_attr_Def_enum(['normal', 'small-caps'], false);
        $uri_or_none = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['none']), new Html_Purifier_attr_Def_css_uri()]);
        $this->info['list-style-position'] = new Html_Purifier_attr_Def_enum(['inside', 'outside'], false);
        $this->info['list-style-type'] = new Html_Purifier_attr_Def_enum(['disc', 'circle', 'square', 'decimal', 'lower-roman', 'upper-roman', 'lower-alpha', 'upper-alpha', 'none'], false);
        $this->info['list-style-image'] = $uri_or_none;
        $this->info['list-style'] = new Html_Purifier_attr_Def_css_list_Style($config);
        $this->info['text-transform'] = new Html_Purifier_attr_Def_enum(['capitalize', 'uppercase', 'lowercase', 'none'], false);
        $this->info['color'] = new Html_Purifier_attr_Def_css_color();
        $this->info['background-image'] = $uri_or_none;
        $this->info['background-repeat'] = new Html_Purifier_attr_Def_enum(['repeat', 'repeat-x', 'repeat-y', 'no-repeat']);
        $this->info['background-attachment'] = new Html_Purifier_attr_Def_enum(['scroll', 'fixed']);
        $this->info['background-position'] = new Html_Purifier_attr_Def_css_background_Position();
        $this->info['background-size'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['auto', 'cover', 'contain']), new Html_Purifier_attr_Def_css_percentage(), new Html_Purifier_attr_Def_css_length()]);
        $border_color = $this->info['border-top-color'] = $this->info['border-bottom-color'] = $this->info['border-left-color'] = $this->info['border-right-color'] = $this->info['background-color'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['transparent']), new Html_Purifier_attr_Def_css_color()]);
        $this->info['background'] = new Html_Purifier_attr_Def_css_background($config);
        $this->info['border-color'] = new Html_Purifier_attr_Def_css_multiple($border_color);
        $border_width = $this->info['border-top-width'] = $this->info['border-bottom-width'] = $this->info['border-left-width'] = $this->info['border-right-width'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['thin', 'medium', 'thick']), new Html_Purifier_attr_Def_css_length('0')]);
        $this->info['border-width'] = new Html_Purifier_attr_Def_css_multiple($border_width);
        $this->info['letter-spacing'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['normal']), new Html_Purifier_attr_Def_css_length()]);
        $this->info['word-spacing'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['normal']), new Html_Purifier_attr_Def_css_length()]);
        $this->info['font-size'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large', 'larger', 'smaller']), new Html_Purifier_attr_Def_css_percentage(), new Html_Purifier_attr_Def_css_length()]);
        $this->info['line-height'] = new Html_Purifier_attr_Def_css_composite([
            new Html_Purifier_attr_Def_enum(['normal']),
            new Html_Purifier_attr_Def_css_number(true),
            // no negatives
            new Html_Purifier_attr_Def_css_length('0'),
            new Html_Purifier_attr_Def_css_percentage(true),
        ]);
        $margin = $this->info['margin-top'] = $this->info['margin-bottom'] = $this->info['margin-left'] = $this->info['margin-right'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length(), new Html_Purifier_attr_Def_css_percentage(), new Html_Purifier_attr_Def_enum(['auto'])]);
        $this->info['margin'] = new Html_Purifier_attr_Def_css_multiple($margin);
        // non-negative
        $padding = $this->info['padding-top'] = $this->info['padding-bottom'] = $this->info['padding-left'] = $this->info['padding-right'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length('0'), new Html_Purifier_attr_Def_css_percentage(true)]);
        $this->info['padding'] = new Html_Purifier_attr_Def_css_multiple($padding);
        $this->info['text-indent'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length(), new Html_Purifier_attr_Def_css_percentage()]);
        $trusted_wh = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length('0'), new Html_Purifier_attr_Def_css_percentage(true), new Html_Purifier_attr_Def_enum(['auto'])]);
        $trusted_min_wh = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length('0'), new Html_Purifier_attr_Def_css_percentage(true)]);
        $trusted_max_wh = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length('0'), new Html_Purifier_attr_Def_css_percentage(true), new Html_Purifier_attr_Def_enum(['none'])]);
        $max = $config->get('CSS.MaxImgLength');
        $this->info['width'] = $this->info['height'] = $max === null ? $trusted_wh : new Html_Purifier_attr_Def_switch(
            'img',
            // For img tags:
            new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length('0', $max), new Html_Purifier_attr_Def_enum(['auto'])]),
            // For everyone else:
            $trusted_wh
        );
        $this->info['min-width'] = $this->info['min-height'] = $max === null ? $trusted_min_wh : new Html_Purifier_attr_Def_switch(
            'img',
            // For img tags:
            new Html_Purifier_attr_Def_css_length('0', $max),
            // For everyone else:
            $trusted_min_wh
        );
        $this->info['max-width'] = $this->info['max-height'] = $max === null ? $trusted_max_wh : new Html_Purifier_attr_Def_switch(
            'img',
            // For img tags:
            new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length('0', $max), new Html_Purifier_attr_Def_enum(['none'])]),
            // For everyone else:
            $trusted_max_wh
        );
        $this->info['aspect-ratio'] = new Html_Purifier_attr_Def_css_multiple(new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_ratio(), new Html_Purifier_attr_Def_enum(['auto'])]));
        // text-decoration and related shorthands
        $this->info['text-decoration'] = new Html_Purifier_attr_Def_css_text_Decoration();
        $this->info['text-decoration-line'] = new Html_Purifier_attr_Def_enum(['none', 'underline', 'overline', 'line-through']);
        $this->info['text-decoration-style'] = new Html_Purifier_attr_Def_enum(['solid', 'double', 'dotted', 'dashed', 'wavy']);
        $this->info['text-decoration-color'] = new Html_Purifier_attr_Def_css_color();
        $this->info['text-decoration-thickness'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length(), new Html_Purifier_attr_Def_css_percentage(), new Html_Purifier_attr_Def_enum(['auto', 'from-font'])]);
        $this->info['font-family'] = new Html_Purifier_attr_Def_css_font_Family();
        // this could use specialized code
        $this->info['font-weight'] = new Html_Purifier_attr_Def_enum(['normal', 'bold', 'bolder', 'lighter', '100', '200', '300', '400', '500', '600', '700', '800', '900'], false);
        // MUST be called after other font properties, as it references
        // a CSSDefinition object
        $this->info['font'] = new Html_Purifier_attr_Def_css_font($config);
        // same here
        $this->info['border'] = $this->info['border-bottom'] = $this->info['border-top'] = $this->info['border-left'] = $this->info['border-right'] = new Html_Purifier_attr_Def_css_border($config);
        $this->info['border-collapse'] = new Html_Purifier_attr_Def_enum(['collapse', 'separate']);
        $this->info['caption-side'] = new Html_Purifier_attr_Def_enum(['top', 'bottom']);
        $this->info['table-layout'] = new Html_Purifier_attr_Def_enum(['auto', 'fixed']);
        $this->info['vertical-align'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_enum(['baseline', 'sub', 'super', 'top', 'text-top', 'middle', 'bottom', 'text-bottom']), new Html_Purifier_attr_Def_css_length(), new Html_Purifier_attr_Def_css_percentage()]);
        $this->info['border-spacing'] = new Html_Purifier_attr_Def_css_multiple(new Html_Purifier_attr_Def_css_length(), 2);
        // These CSS properties don't work on many browsers, but we live
        // in THE FUTURE!
        $this->info['white-space'] = new Html_Purifier_attr_Def_enum(['nowrap', 'normal', 'pre', 'pre-wrap', 'pre-line']);
        if ($config->get('CSS.Proprietary')) {
            $this->do_setup_proprietary($config);
        }
        if ($config->get('CSS.AllowTricky')) {
            $this->do_setup_tricky($config);
        }
        if ($config->get('CSS.Trusted')) {
            $this->do_setup_trusted($config);
        }
        $allow_important = $config->get('CSS.AllowImportant');
        // wrap all attr-defs with decorator that handles !important
        foreach ($this->info as $k => $v) {
            $this->info[$k] = new Html_Purifier_attr_Def_css_important_Decorator($v, $allow_important);
        }
        $this->setup_config_stuff($config);
    }
    /**
     * @param HTMLPurifier_Config $config
     */
    protected function do_setup_proprietary($config)
    {
        // Internet Explorer only scrollbar colors
        $this->info['scrollbar-arrow-color'] = new Html_Purifier_attr_Def_css_color();
        $this->info['scrollbar-base-color'] = new Html_Purifier_attr_Def_css_color();
        $this->info['scrollbar-darkshadow-color'] = new Html_Purifier_attr_Def_css_color();
        $this->info['scrollbar-face-color'] = new Html_Purifier_attr_Def_css_color();
        $this->info['scrollbar-highlight-color'] = new Html_Purifier_attr_Def_css_color();
        $this->info['scrollbar-shadow-color'] = new Html_Purifier_attr_Def_css_color();
        // vendor specific prefixes of opacity
        $this->info['-moz-opacity'] = new Html_Purifier_attr_Def_css_alpha_Value();
        $this->info['-khtml-opacity'] = new Html_Purifier_attr_Def_css_alpha_Value();
        // only opacity, for now
        $this->info['filter'] = new Html_Purifier_attr_Def_css_filter();
        // more CSS3
        $this->info['page-break-after'] = $this->info['page-break-before'] = new Html_Purifier_attr_Def_enum(['auto', 'always', 'avoid', 'left', 'right']);
        $this->info['page-break-inside'] = new Html_Purifier_attr_Def_enum(['auto', 'avoid']);
        $border_radius = new Html_Purifier_attr_Def_css_composite([
            new Html_Purifier_attr_Def_css_percentage(true),
            // disallow negative
            new Html_Purifier_attr_Def_css_length('0'),
        ]);
        $this->info['border-top-left-radius'] = $this->info['border-top-right-radius'] = $this->info['border-bottom-right-radius'] = $this->info['border-bottom-left-radius'] = new Html_Purifier_attr_Def_css_multiple($border_radius, 2);
        // TODO: support SLASH syntax
        $this->info['border-radius'] = new Html_Purifier_attr_Def_css_multiple($border_radius, 4);
    }
    /**
     * @param HTMLPurifier_Config $config
     */
    protected function do_setup_tricky($config)
    {
        $this->info['display'] = new Html_Purifier_attr_Def_enum(['inline', 'block', 'list-item', 'run-in', 'compact', 'marker', 'table', 'inline-block', 'inline-table', 'table-row-group', 'table-header-group', 'table-footer-group', 'table-row', 'table-column-group', 'table-column', 'table-cell', 'table-caption', 'none']);
        $this->info['visibility'] = new Html_Purifier_attr_Def_enum(['visible', 'hidden', 'collapse']);
        $this->info['overflow'] = new Html_Purifier_attr_Def_enum(['visible', 'hidden', 'auto', 'scroll']);
        $this->info['opacity'] = new Html_Purifier_attr_Def_css_alpha_Value();
    }
    /**
     * @param HTMLPurifier_Config $config
     */
    protected function do_setup_trusted($config)
    {
        $this->info['position'] = new Html_Purifier_attr_Def_enum(['static', 'relative', 'absolute', 'fixed']);
        $this->info['top'] = $this->info['left'] = $this->info['right'] = $this->info['bottom'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_css_length(), new Html_Purifier_attr_Def_css_percentage(), new Html_Purifier_attr_Def_enum(['auto'])]);
        $this->info['z-index'] = new Html_Purifier_attr_Def_css_composite([new Html_Purifier_attr_Def_integer(), new Html_Purifier_attr_Def_enum(['auto'])]);
    }
    /**
     * Performs extra config-based processing. Based off of
     * HTMLPurifier_HTMLDefinition.
     * @param HTMLPurifier_Config $config
     * @todo Refactor duplicate elements into common class (probably using
     *       composition, not inheritance).
     */
    protected function setup_config_stuff($config)
    {
        // setup allowed elements
        $support = '(for information on implementing this, see the ' . 'support forums) ';
        $allowed_properties = $config->get('CSS.AllowedProperties');
        if ($allowed_properties !== null) {
            foreach ($this->info as $name => $d) {
                if (!isset($allowed_properties[$name])) {
                    unset($this->info[$name]);
                }
                unset($allowed_properties[$name]);
            }
            // emit errors
            foreach ($allowed_properties as $name => $d) {
                // :TODO: Is this htmlspecialchars() call really necessary?
                $name = htmlspecialchars($name);
                trigger_error("Style attribute '{$name}' is not supported {$support}", E_USER_WARNING);
            }
        }
        $forbidden_properties = $config->get('CSS.ForbiddenProperties');
        if ($forbidden_properties !== null) {
            foreach ($this->info as $name => $d) {
                if (isset($forbidden_properties[$name])) {
                    unset($this->info[$name]);
                }
            }
        }
    }
}
// vim: et sw=4 sts=4