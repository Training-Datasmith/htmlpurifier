<?php

declare (strict_types=1);
/**
 * Pre-transform that changes deprecated name attribute to ID if necessary
 */
class Html_Purifier_attr_Transform_name extends Html_Purifier_attr_Transform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        // Abort early if we're using relaxed definition of name
        if ($config->get('HTML.Attr.Name.UseCDATA')) {
            return $attr;
        }
        if (!isset($attr['name'])) {
            return $attr;
        }
        $id = $this->confiscate_attr($attr, 'name');
        if (isset($attr['id'])) {
            return $attr;
        }
        $attr['id'] = $id;
        return $attr;
    }
}
// vim: et sw=4 sts=4