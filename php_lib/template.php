<?php

class Template
{
    private $text;
    private $title;
    private $desc;
    private $js;
    private $css;
    private $tabs = array();
    private $template_file;

    function __construct($template)
    {
        $this->template_file = $template;
    }

    #adds a tab to the template to allow multiple lists concurrently
    function AddTab(Tab $tab)
    {
        $this->tabs[] = $tab;
    }

    #substitute text placeholders for actual strings
    function GetText()
    {
        if (!file_exists($this->template_file))
            return("File " . $this->template_file . " not found!");

        $output = file_get_contents($this->template_file);
        //$output = $this->text;
        $output = str_replace('@title',$this->title,$output);
        $output = str_replace('@desc',$this->desc,$output);
        //$output = str_replace('@listname',$this->listname,$output);
        //$output = str_replace('@long_desc_subtitle',$this->long_desc_subtitle,$output);
        //$output = str_replace('@long_desc',$this->long_desc,$output);
        //$output = str_replace('@content',$this->content,$output);
        $output = str_replace('@js',$this->js,$output);
        $output = str_replace('@css',$this->css,$output);
        //$output = str_replace('@header',$this->header,$output);
        $output = str_replace('@tabs',$this->GetTabsString(),$output);
        $output = str_replace('@tab_section',$this->GetAllTabsHTML(),$output);
        return $output;
    }

    private function GetTabsString()
    {
        $output = '<ul>';
        foreach($this->tabs as $num => $tab)
                $output .= '<li data-tab-nav="'.$tab->GetName().'"><a href="javascript:void(0)">'.$tab->GetName().'</a></li>';
        $output .= '</ul>';
        return $output;
    }

    function GetAllTabsHTML()
    {
        $html = "";
        foreach($this->tabs as $tab)
            $html .= $tab->GenerateTabHTML();
        return $html;
    }

    function SetDesc($d)
    {
        $this->desc = $d;
    }

    function SetTitle($t)
    {
        $this->title = $t;
    }
    
    function AddJSFile($filename)
    {
        $file = file_get_contents($filename);
        $this->js .= $file."\n";
    }
    
    function AddCSSFile($filename)
    {
        $file = file_get_contents($filename);
        $this->css .= $file."\n";
    }
}

?>
