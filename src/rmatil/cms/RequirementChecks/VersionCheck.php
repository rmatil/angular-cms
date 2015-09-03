<?php

namespace rmatil\cms\Checks;

class VersionCheck implements IRequirementCheck {
    
    public function checkRequirement() {
        return version_compare(PHP_VERSION, '5.6.0', '<');
    }

}
