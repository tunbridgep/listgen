<?php

class Tab
{
    private $listname;
    private $desc;
    private $long_desc;
    private $long_desc_subtitle;
    private $path;
    private $sections = array();
    private $hash;

    public function __construct($name,$desc,$subtitle,$path,$hash)
    {
        $this->listname = $name;
        $this->long_desc = $desc;
        $this->long_desc_subtitle = $subtitle;
        $this->path = $path;
        $this->hash = $hash;
        $this->PopulateTab();
    }
    
    public function SetListName($l)
    {
        $this->listname = $l;
    }

    public function SetDesc($d)
    {
        $this->desc = $d;
    }

    public function SetLongDesc($d)
    {
        $this->long_desc = $d;
    }

    public function SetSubtitle($s)
    {
        $this->long_desc_subtitle = $s;
    }

    private function PopulateTab()
    {
        global $default_link_location;
        global $default_link_space_character;

        $folders = $this->path."/*";
        #echo $folders."\n";
        foreach(glob($folders) as $section)
        {
            #echo "\t".$section."\n";
            $hash = hash('md5',basename($section));
            #$s = new Section($default_link_location,$default_link_space_character,$hash,$this->tab_counter);
            $s = new Section($default_link_location,$default_link_space_character,$hash,$this->hash);
            $s->GenerateSection($section);

            $this->sections[] = $s;
        }
    }

    public function GetName()
    {
        return $this->listname;
    }

    private function GetContent()
    {
        $content = "";
        foreach($this->sections as $section)
        {
            $content .= $section->GetContentString();
        }
        return $content;
    }

    private function GetHeader()
    {
        $html = "<ul>";
        foreach($this->sections as $section)
        {
            $html .= '<li><a href="#'.$section->GetSectionLinkName().'">'.$section->GetNiceName()."</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    public function GenerateTabHTML()
    {
        $html = '<div class="tab-pane" data-tab="'.$this->listname.'" id="tab'.$this->listname.'">'.
                #"\t".'<div id="intro">'."\n".
                #"\t\t".'<p>'.$this->long_desc.'</p>'."\n".
                #"\t\t".'<p><em>'.$this->long_desc_subtitle.'</em></p>'.
                #"\t".'</div>'.
                "\t".'<div class="ListHeaderSection">'.
                "\t\t".$this->GetHeader().
                "\t</div>".
                "\t".'<div class="ListContentSection">'.
                   $this->GetContent().
                "\t</div>".
                "</div>";

        return $html;
    }
}

?>
