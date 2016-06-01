<?php


namespace rmatil\CmsBundle\DataAccessor;


use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use rmatil\CmsBundle\Constants\EntityNames;
use rmatil\CmsBundle\Controller\UpdateUserGroupTrait;
use rmatil\CmsBundle\Entities\Language;
use rmatil\CmsBundle\Entities\Page;
use rmatil\CmsBundle\Entities\PageCategory;
use rmatil\CmsBundle\Exception\EntityInvalidException;
use rmatil\CmsBundle\Exception\EntityNotDeletedException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;
use rmatil\CmsBundle\Exception\EntityNotInsertedException;
use rmatil\CmsBundle\Exception\EntityNotUpdatedException;

class PageDataAccessor extends DataAccessor {

    use UpdateUserGroupTrait;

    public function __construct($em, $logger) {
        parent::__construct(EntityNames::PAGE, $em, $logger);
    }

    public function update($page) {
        if ( ! ($page instanceof Page)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', EntityNames::PAGE, get_class($page)));
        }

        /** @var \rmatil\CmsBundle\Entities\Page $dbPage */
        $dbPage = $this->em->getRepository(EntityNames::PAGE)->find($page->getId());

        if (null === $dbPage) {
            throw new EntityNotFoundException(sprintf('Entity "%s" with id "%s" not found', $this->entityName, $page->getId()));
        }

        if ($page->getLanguage() instanceof Language) {
            $page->setLanguage(
                $this->em->getRepository(EntityNames::LANGUAGE)->find($page->getLanguage()->getId())
            );
        }

        if ($page->getCategory() instanceof PageCategory) {
            $page->setCategory(
                $this->em->getRepository(EntityNames::PAGE_CATEGORY)->find($page->getCategory()->getId())
            );
        }

        $allUserGroups = $this->em->getRepository(EntityNames::USER_GROUP)->findAll();

        $this->updateUserGroups($allUserGroups, $page, $dbPage);

        // remove all articles, then add only these who are checked
        foreach ($dbPage->getArticles()->toArray() as $article) {
            $dbArticle = $this->em->getRepository(EntityNames::ARTICLE)->find($article->getId());
            $dbArticle->setPage(null);
        }
        $dbPage->setArticles(null);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotUpdatedException($dbalex->getMessage());
        }

        if (null !== $page->getArticles()) {
            $dbArticles = new ArrayCollection();
            foreach ($page->getArticles()->toArray() as $article) {
                $dbArticle = $this->em->getRepository(EntityNames::ARTICLE)->find($article->getId());

                $dbArticle->setPage($dbPage);
                $dbArticles->add($dbArticle);
            }
            $dbPage->setArticles($dbArticles);
        }

        // Note: we prevent updating title and url name due to the uniqid
        // stored in url-name. Otherwise, permanent links would fail
        $dbPage->setAuthor($page->getAuthor());
        $dbPage->setCategory($page->getCategory());
        $dbPage->setLanguage($page->getLanguage());
        $dbPage->setParent($page->getParent());
        $dbPage->setCreationDate($page->getCreationDate());
        $dbPage->setHasSubnavigation($page->getHasSubnavigation());
        $dbPage->setIsPublished($page->getIsPublished());
        $dbPage->setLastEditDate($page->getLastEditDate());
        $dbPage->setIsStartPage($page->getIsStartPage());

        $now = new DateTime('now', new DateTimeZone("UTC"));
        $dbPage->setLastEditDate($now);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotUpdatedException($dbalex->getMessage());
        }

        return $dbPage;
    }

    public function insert($page) {
        if ( ! ($page instanceof Page)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', EntityNames::PAGE, get_class($page)));
        }

        if ($page->getLanguage() instanceof Language) {
            $page->setLanguage(
                $this->em->getRepository(EntityNames::LANGUAGE)->find($page->getLanguage()->getId())
            );
        }

        if ($page->getCategory() instanceof PageCategory) {
            $page->setCategory(
                $this->em->getRepository(EntityNames::PAGE_CATEGORY)->find($page->getCategory()->getId())
            );
        }

        $allUserGroups = $this->em->getRepository(EntityNames::USER_GROUP)->findAll();
        $this->insertUserGroups($allUserGroups, $page);

        $origArticles = new ArrayCollection();
        $articleRepository = $this->em->getRepository(EntityNames::ARTICLE);
        // get origArticles
        foreach ($page->getArticles()->toArray() as $article) {
            /** @var \rmatil\CmsBundle\Entities\Article $origArticle */
            $origArticle = $articleRepository->findOneBy(array('id' => $article->getId()));
            $origArticle->setPage($page);
            $origArticles->add($origArticle);
        }
        $page->setArticles($origArticles);

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $page->setLastEditDate($now);
        $page->setCreationDate($now);

        $uniqid = uniqid();
        $page->setUrlName(sprintf('%s-%s', $page->getUrlName(), $uniqid));

        $this->em->persist($page);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s"', $this->entityName));
        }

        return $page;
    }

    public function delete($id) {
        $dbPage = $this->em->getRepository(EntityNames::PAGE)->find($id);

        if (null === $dbPage) {
            throw new EntityNotFoundException(sprintf('Could not foudn Entity "%s" with id "%s"', $this->entityName, $id));
        }

        // remove all articles
        foreach ($dbPage->getArticles() as $article) {
            $article->setPage(null);
        }

        $this->em->remove($dbPage);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotDeletedException($dbalex->getMessage());
        }
    }
}
