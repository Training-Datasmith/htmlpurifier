<?php

declare (strict_types=1);
/**
 * Converts HTMLPurifier_ConfigSchema_Interchange to an XML format,
 * which can be further processed to generate documentation.
 */
class Html_Purifier_config_Schema_builder_xml extends Xml_Writer
{
    /**
     * @type HTMLPurifier_ConfigSchema_Interchange
     */
    protected $interchange;
    /**
     * @type string
     */
    private $namespace;
    /**
     * @param string $html
     */
    protected function write_html_div($html)
    {
        $this->start_element('div');
        $purifier = Html_Purifier::get_instance();
        $html = $purifier->purify($html);
        $this->write_attribute('xmlns', 'http://www.w3.org/1999/xhtml');
        $this->write_raw($html);
        $this->end_element();
        // div
    }
    /**
     * @param mixed $var
     * @return string
     */
    protected function export($var)
    {
        if ($var === []) {
            return 'array()';
        }
        return var_export($var, true);
    }
    /**
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     */
    public function build($interchange)
    {
        // global access, only use as last resort
        $this->interchange = $interchange;
        $this->set_indent(true);
        $this->start_document('1.0', 'UTF-8');
        $this->start_element('configdoc');
        $this->write_element('title', $interchange->name);
        foreach ($interchange->directives as $directive) {
            $this->build_directive($directive);
        }
        if ($this->namespace) {
            $this->end_element();
        }
        // namespace
        $this->end_element();
        // configdoc
        $this->flush();
    }
    /**
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $directive
     */
    public function build_directive($directive)
    {
        // Kludge, although I suppose having a notion of a "root namespace"
        // certainly makes things look nicer when documentation is built.
        // Depends on things being sorted.
        if (!$this->namespace || $this->namespace !== $directive->id->get_root_namespace()) {
            if ($this->namespace) {
                $this->end_element();
            }
            // namespace
            $this->namespace = $directive->id->get_root_namespace();
            $this->start_element('namespace');
            $this->write_attribute('id', $this->namespace);
            $this->write_element('name', $this->namespace);
        }
        $this->start_element('directive');
        $this->write_attribute('id', $directive->id->to_string());
        $this->write_element('name', $directive->id->get_directive());
        $this->start_element('aliases');
        foreach ($directive->aliases as $alias) {
            $this->write_element('alias', $alias->to_string());
        }
        $this->end_element();
        // aliases
        $this->start_element('constraints');
        if ($directive->version) {
            $this->write_element('version', $directive->version);
        }
        $this->start_element('type');
        if ($directive->type_allows_null) {
            $this->write_attribute('allow-null', 'yes');
        }
        $this->text($directive->type);
        $this->end_element();
        // type
        if ($directive->allowed) {
            $this->start_element('allowed');
            foreach ($directive->allowed as $value => $x) {
                $this->write_element('value', $value);
            }
            $this->end_element();
            // allowed
        }
        $this->write_element('default', $this->export($directive->default));
        $this->write_attribute('xml:space', 'preserve');
        if ($directive->external) {
            $this->start_element('external');
            foreach ($directive->external as $project) {
                $this->write_element('project', $project);
            }
            $this->end_element();
        }
        $this->end_element();
        // constraints
        if ($directive->deprecated_version) {
            $this->start_element('deprecated');
            $this->write_element('version', $directive->deprecated_version);
            $this->write_element('use', $directive->deprecated_use->to_string());
            $this->end_element();
            // deprecated
        }
        $this->start_element('description');
        $this->write_html_div($directive->description);
        $this->end_element();
        // description
        $this->end_element();
        // directive
    }
}
// vim: et sw=4 sts=4