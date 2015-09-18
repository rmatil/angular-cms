<?php


namespace rmatil\cms\Twig;


use rmatil\cms\Constants\EntityNames;
use Twig_Extension;
use Twig_Function_Method;

class FileUtilities extends Twig_Extension {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getFunctions() {
        return array(
            'getFilesByExtension' => new Twig_Function_Method($this, 'getFilesByExtension'),
        );
    }

    public function getName() {
        return 'file_utilities_extension';
    }

    public function getFilesByExtension($extension) {
        $files = $this->em->getRepository(EntityNames::FILE)->findBy(array(
            'extension' => $extension
        ));

        return $files;
    }
}