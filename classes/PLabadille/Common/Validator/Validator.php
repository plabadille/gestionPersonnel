<?php
namespace PLabadille\Common\Validator;

class Validator
{
    protected $strategies=array();

    public function addStrategy($strategy)
    {
        $this->strategies[]=$strategy;   
    }

    public function applyStrategies($value)
    {
        foreach ($this->strategies as $strategy) {
            $error = $strategy->validate($value);
            if ($error !== null){
                return $error;
            }
        }
        return null;
    }
}