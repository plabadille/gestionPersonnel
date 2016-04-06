<?php
namespace PLabadille\Common\Cleaner;

class Cleaner
{
    protected $strategies=array();


    public function addStrategy($strategy)
    {
        $this->strategies[]=$strategy;   
    }

    public function applyStrategies($value)
    {
        foreach ($this->strategies as $strategy) {
            $value = $strategy->clean($value);
        }
        return $value;
    }
}