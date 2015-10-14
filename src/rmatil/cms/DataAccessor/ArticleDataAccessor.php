<?php


namespace rmatil\cms\DataAccessor;


use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Controller\UpdateUserGroupTrait;
use rmatil\cms\Entities\Article;
use rmatil\cms\Entities\ArticleCategory;
use rmatil\cms\Entities\Language;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;

class ArticleDataAccessor extends DataAccessor {

    use UpdateUserGroupTrait;

    public function __construct($em, $logger) {
        parent::__construct(EntityNames::ARTICLE, $em, $logger);
    }

    public function update($article) {
        if ( ! ($article instanceof Article)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', EntityNames::ARTICLE, get_class($article)));
        }

        $dbArticle = $this->em->getRepository($this->entityName)->find($article->getId());

        if (null === $dbArticle) {
            throw new EntityNotFoundException(sprintf('Entity "%s" with id "%s" not found', $this->entityName, $article->getId()));
        }

        if ($article->getLanguage() instanceof Language) {
            $article->setLanguage(
                $this->em->getRepository(EntityNames::LANGUAGE)->find($article->getLanguage()->getId())
            );
        }

        if ($article->getAuthor() instanceof User) {
            $article->setAuthor(
                $this->em->getRepository(EntityNames::USER)->find($article->getAuthor()->getId())
            );
        }

        if ($article->getCategory() instanceof ArticleCategory) {
            $article->setCategory(
                $this->em->getRepository(EntityNames::ARTICLE_CATEGORY)->find($article->getCategory()->getId())
            );
        }

        $allUserGroups = $this->em->getRepository(EntityNames::USER_GROUP)->findAll();

        $this->updateUserGroups($allUserGroups, $article, $dbArticle);

        $dbArticle->setTitle($article->getTitle());
        $dbArticle->setContent($article->getContent());
        $dbArticle->setUrlName($article->getUrlName());
        $dbArticle->setIsPublished($article->getIsPublished());
        $dbArticle->setCategory($article->getCategory());
        $dbArticle->setAuthor($article->getAuthor());
        $dbArticle->setLanguage($article->getLanguage());
        $dbArticle->setLastEditDate($article->getLastEditDate());
        $dbArticle->setCreationDate($article->getCreationDate());

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotUpdatedException(sprintf('Could not update entity "%s" with id "%s"', $this->entityName, $article->getId()));
        }

        return $article;
    }

    public function insert($article) {
        if ( ! ($article instanceof Article)) {
            throw new EntityInvalidException('Required object of type "%s" but got "%s"', EntityNames::ARTICLE, get_class($article));
        }

        if ($article->getLanguage() instanceof Language) {
            $article->setLanguage(
                $this->em->getRepository(EntityNames::LANGUAGE)->find($article->getLanguage()->getId())
            );
        }

        if ($article->getAuthor() instanceof User) {
            $article->setAuthor(
                $this->em->getRepository(EntityNames::USER)->find($article->getAuthor()->getId())
            );
        }

        if ($article->getCategory() instanceof ArticleCategory) {
            $article->setCategory(
                $this->em->getRepository(EntityNames::ARTICLE_CATEGORY)->find($article->getCategory()->getId())
            );
        }

        $allUserGroups = $this->em->getRepository(EntityNames::USER_GROUP)->findAll();
        $this->insertUserGroups($allUserGroups, $article);

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $article->setLastEditDate($now);
        $article->setCreationDate($now);

        $this->em->persist($article);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s"', $this->entityName));
        }

        return $article;
    }

}