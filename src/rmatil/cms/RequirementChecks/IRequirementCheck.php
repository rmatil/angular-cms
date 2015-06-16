<?php

namespace rmatil\cms\Checks;

interface IRequirementCheck {
    
    /**
     * Returns true, if the requirement was held,
     * otherwise false
     * 
     * @return bool true|false True, if the requirement held, otherwise false
     */
    public function checkRequirement();
    
}
