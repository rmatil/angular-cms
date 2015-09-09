<?php


namespace rmatil\cms\Twig;


use rmatil\cms\Constants\EntityNames;
use Twig_Environment;
use Twig_Extension;
use Twig_Function_Method;

class MetadataFunction extends Twig_Extension {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getFunctions() {
        return array(
            'getSiteMetadata' => new Twig_Function_Method($this, 'getSiteMetadata'),
            'getMenuEntries' => new Twig_Function_Method($this, 'getMenuEntries')
        );
    }

    public function getName() {
        return 'metadata_function_extension';
    }


    public function getSiteMetadata() {
        $settingRepo = $this->em->getRepository(EntityNames::SETTING);

        $websiteName = $settingRepo->findOneBy(array('name' => 'website_name'));
        $websiteDesc = $settingRepo->findOneBy(array('name' => 'website_description'));
        $websiteKeyWords = $settingRepo->findOneBy(array('name' => 'website_keywords'));

        return array(
            'website_name' => $websiteName,
            'website_desc' => $websiteDesc,
            'website_keywords' => $websiteKeyWords
        );
    }

    public function getMenuEntries() {
        $pages = $this->em->createQueryBuilder()
            ->select('p')
            ->from(EntityNames::PAGE, 'p')
            ->where('p.isPublished = true')
            ->getQuery()
            ->getResult();

        return $pages;

    }
}