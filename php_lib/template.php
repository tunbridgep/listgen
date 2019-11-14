<?php

class Template
{
    private $text;
    private $title;
    private $desc;
    private $js_includes = array();
    private $js_links = array();
    private $css_includes = array();
    private $css_links = array();
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
        $output = str_replace('@js',$this->GetJSString(),$output);
        $output = str_replace('@css',$this->GetCSSString(),$output);
        //$output = str_replace('@header',$this->header,$output);
        $output = str_replace('@tabs',$this->GetTabsString(),$output);
        $output = str_replace('@tab_section',$this->GetAllTabsHTML(),$output);
        $output = str_replace('@filters',$this->GetFilterString(),$output);

        return $output;
    }

    private function GetJSString()
    {
        $re = "";
        if (sizeof($this->js_includes) > 0)
        {
            $re .= '<script type="text/javascript">'.PHP_EOL;
            foreach($this->js_includes as $include)
                $re .= $include.PHP_EOL;
            $re .= "</script>".PHP_EOL;
        }

        foreach($this->js_links as $link)
            $re .= '<script type="text/javascript" src="'.$link.'"></script>'.PHP_EOL;

        return $re;

    }
    
    private function GetCSSString()
    {
        $re = "";
        if (sizeof($this->css_includes) > 0)
        {
            $re .= '<style type="text/css">'.PHP_EOL;
            foreach($this->css_includes as $include)
                $re .= $include.PHP_EOL;
            $re .= "</style>".PHP_EOL;
        }

        foreach($this->css_links as $link)
            $re .= '<link rel="stylesheet" type="text/css" href="'.$link.'" />'.PHP_EOL;

        return $re;

    }

    private function GetFilterString()
    {
        global $filters;
        if (count($filters) > 0)
        {
            $output = "<ul>";
            $output .= '<li data-filter-none="No Filter"><a href="javascript:void(0)">No Filter</a></li>';
            foreach($filters as $num => $filter)
            {
                if ($filter instanceof Filter)
                    $output .= '<li data-filter="'.$filter->GetFilter().'"><a href="javascript:void(0)">'.$filter->GetName().'</a></li>';
            }
            $output .= "</ul>";
            return $output;
        }
    }

    private function GetTabsString()
    {
        $output = '<ul>';
        foreach($this->tabs as $num => $tab)
                $output .= '<li data-tab-nav="'.$tab->GetName().'"><a href="javascript:void(0)">'.$tab->GetNiceName().'</a></li>';
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
    
    function AddArbitraryCSS(string $css)
    {
        $this->js_includes[] = $css;
    }

    function AddArbitraryJS(string $js)
    {
        $this->js_includes[] = $js;
    }

    function AddJSFile(string $filename)
    {
        if (!file_exists($filename))
            die("required JS file ".$filename." was not found!");
        $this->js_includes[] = file_get_contents($filename);
    }
    
    function AddCSSFile(string $filename)
    {
        if (!file_exists($filename))
            die("required CSS file ".$filename." was not found!");
        $this->css_includes[] = file_get_contents($filename);
    }
    
    function AddJSLink(string $filename)
    {
        $this->js_links[] = $filename;
    }
    
    function AddCSSLink(string $filename)
    {
        $this->css_links[] = $filename;
    }
}

?>
