<?php


namespace rmatil\cms\Twig;


use rmatil\cms\Constants\EntityNames;
use Twig_Function_Method;

class EventUtilities extends \Twig_Extension {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getFunctions() {
        return array(
            'getProtectedEvents' => new Twig_Function_Method($this, 'getProtectedEvents'),
        );
    }

    public function getName() {
        return 'event_utilities_extension';
    }

    public function getProtectedEvents() {
        $events = $this->em->createQueryBuilder()
            ->select('e')
            ->from(EntityNames::EVENT, 'e')
            ->where('e.allowedUserGroups IS NOT EMPTY')
            ->getQuery()
            ->getResult();

        return $events;
    }
}