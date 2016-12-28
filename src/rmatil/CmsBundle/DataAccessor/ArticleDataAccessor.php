<?php


namespace rmatil\CmsBundle\DataAccessor;


use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use rmatil\CmsBundle\Constants\EntityNames;
use rmatil\CmsBundle\Entity\Article;
use rmatil\CmsBundle\Entity\ArticleCategory;
use rmatil\CmsBundle\Entity\Language;
use rmatil\CmsBundle\Entity\User;
use rmatil\CmsBundle\Exception\EntityInvalidException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;
use rmatil\CmsBundle\Exception\EntityNotInsertedException;
use rmatil\CmsBundle\Exception\EntityNotUpdatedException;
use rmatil\CmsBundle\Mapper\ArticleMapper;
use rmatil\CmsBundle\Model\ArticleDTO;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleDataAccessor extends DataAccessor {

    use UpdateUserGroupTrait;

    protected $articleMapper;

    public function __construct(EntityManagerInterface $em, ArticleMapper $articleMapper, TokenStorageInterface $tokenStorage, LoggerInterface $logger) {
        parent::__construct(EntityNames::ARTICLE, $em, $tokenStorage, $logger);

        $this->articleMapper = $articleMapper;
    }

    public function getAll() {
        return $this->articleMapper->entitiesToDtos(parent::getAll());
    }

    public function getById($id) {
        return $this->articleMapper->entityToDto(parent::getById($id));
    }

    public function update($articleDto) {
        if ( ! ($articleDto instanceof ArticleDTO)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', ArticleDTO::class, get_class($articleDto)));
        }

        $article = $this->articleMapper->dtoToEntity($articleDto);

        /** @var Article $dbArticle */
        $dbArticle = $this->em->getRepository($this->entityName)->find($article->getId());

        if (null === $dbArticle) {
            throw new EntityNotFoundException(sprintf('Entity "%s" with id "%s" not found', $this->entityName, $article->getId()));
        }

        if ($article->getLanguage() instanceof Language) {
            $article->setLanguage(
                $this->em->getRepository(EntityNames::LANGUAGE)->find($article->getLanguage()->getId())
            );
        }

        // author is current logged in user
        $article->setAuthor(
            $this->em->getRepository(EntityNames::USER)->findOneBy(
                ['username' => $this->tokenStorage->getToken()->getUsername()]
            )
        );

        if ($article->getCategory() instanceof ArticleCategory) {
            $article->setCategory(
                $this->em->getRepository(EntityNames::ARTICLE_CATEGORY)->find($article->getCategory()->getId())
            );
        }

        // Note: we prevent updating url name due to the uniqid
        // stored in url-name. Otherwise, permanent links would fail
        $dbArticle->setTitle($article->getTitle());
        $dbArticle->setContent($article->getContent());
        $dbArticle->setIsPublished($article->getIsPublished());
        $dbArticle->setCategory($article->getCategory());
        $dbArticle->setAuthor($article->getAuthor());
        $dbArticle->setLanguage($article->getLanguage());
        $dbArticle->setCreationDate($article->getCreationDate());
        $dbArticle->setLastEditDate(new DateTime('now', new DateTimeZone('UTC')));


        $allowedUserGroup = $article->getAllowedUserGroup();
        if (null !== $allowedUserGroup) {
            $dbArticle->setAllowedUserGroup(
                $this->em->getRepository(EntityNames::USER_GROUP)->find($article->getAllowedUserGroup()->getId())
            );
        } else {
            $dbArticle->setAllowedUserGroup(null);
        }


        try {
            $this->em->flush();

        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotUpdatedException(sprintf('Could not update entity "%s" with id "%s"', $this->entityName, $article->getId()));
        }

        return $this->articleMapper->entityToDto($dbArticle);
    }

    public function insert($articleDto) {
        if ( ! ($articleDto instanceof ArticleDTO)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', ArticleDTO::class, get_class($articleDto)));
        }

        $article = $this->articleMapper->dtoToEntity($articleDto);

        if ($article->getLanguage() instanceof Language) {
            $article->setLanguage(
                $this->em->getRepository(EntityNames::LANGUAGE)->find($article->getLanguage()->getId())
            );
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $article->setAuthor(
            $this->em->getRepository(EntityNames::USER)->find($user->getId())
        );


        if ($article->getCategory() instanceof ArticleCategory) {
            $article->setCategory(
                $this->em->getRepository(EntityNames::ARTICLE_CATEGORY)->find($article->getCategory()->getId())
            );
        }

        $allowedUserGroup = $article->getAllowedUserGroup();
        if (null !== $allowedUserGroup) {
            $article->setAllowedUserGroup(
                $this->em->getRepository(EntityNames::USER_GROUP)->find($article->getAllowedUserGroup()->getId())
            );
        }

        $uniqid = uniqid();
        $article->setUrlName(sprintf('%s-%s', $article->getUrlName(), $uniqid));

        $this->em->persist($article);

        try {
            $this->em->flush();

        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s"', $this->entityName));
        }

        return $this->articleMapper->entityToDto($article);
    }

}
