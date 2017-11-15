<?php

class Template
{
    private $text;
    private $title;
    private $listname;
    private $desc;
    private $long_desc;
    private $long_desc_subtitle;
    private $content;
    private $js;
    private $css;

    function __construct()
    {
        global $template;
        $this->text = file_get_contents($template);
    }

    #substitute text placeholders for actual strings
    function GetText()
    {
        $output = $this->text;
        $output = str_replace('@title',$this->title,$output);
        $output = str_replace('@listname',$this->listname,$output);
        $output = str_replace('@long_desc_subtitle',$this->long_desc_subtitle,$output);
        $output = str_replace('@long_desc',$this->long_desc,$output);
        $output = str_replace('@content',$this->content,$output);
        $output = str_replace('@js',$this->js,$output);
        $output = str_replace('@css',$this->css,$output);
        return $output;
    }

    function SetTitle($t)
    {
        $this->title = $t;
    }
    
    function SetListName($l)
    {
        $this->listname = $l;
    }

    function SetDesc($d)
    {
        $this->desc = $d;
    }

    function SetLongDesc($d)
    {
        $this->long_desc = $d;
    }
    
    function SetSubtitle($s)
    {
        $this->long_desc_subtitle = $s;
    }

    function SetContent($c)
    {
        $this->content = $c;
    }
    
    function AddContent($c,$separator)
    {
        $this->content = $this->content . $separator . $c;
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
