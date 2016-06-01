<?php


namespace rmatil\CmsBundle\Serializer;


use JMS\Serializer\JsonSerializationVisitor as BaseVisitor;

class JsonSerializationVisitor extends BaseVisitor {
    
    public function getResult() {
        //EXPLICITLY CAST TO ARRAY
        $result = @json_encode($this->getRoot(), $this->getOptions());

        if (null !== ($root = $this->getRoot())) {
            // If root is ArrayObject convert to vanilla array
            if (is_object($root) && $root instanceof \ArrayObject) {
                $root = $root->getArrayCopy();
                $result = @json_encode($root, $this->getOptions());
            }
        }


        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $result;

            case JSON_ERROR_UTF8:
                throw new \RuntimeException('Your data could not be encoded because it contains invalid UTF8 characters.');

            default:
                throw new \RuntimeException(sprintf('An error occurred while encoding your data (error code %d).', json_last_error()));
        }
    }
}
