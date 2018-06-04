<?php

class Filter
{
    private $name;
    private $filter;

    public function __construct(string $name,string $filter)
    {
        $this->name = $name;
        $this->filter = $filter;
    }

    public function GetName()
    {
        return $this->name;
    }
    
    public function GetFilter()
    {
        return $this->filter;
    }
};

$filters = array();

function AddFilter($name,$filter_text)
{
    global $filters;
    array_push($filters,new Filter($name,$filter_text));
}

?>
