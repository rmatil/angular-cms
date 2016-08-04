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
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class ArticleDataAccessor extends DataAccessor {

    use UpdateUserGroupTrait;

    protected $articleMapper;

    public function __construct(EntityManagerInterface $em, ArticleMapper $articleMapper, MutableAclProviderInterface $aclProvider, LoggerInterface $logger) {
        parent::__construct(EntityNames::ARTICLE, $em, $aclProvider, $logger);

        $this->articleMapper = $articleMapper;
    }

    public function getAll() {
        return $this->articleMapper->entitiesToDtos(parent::getAll());
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

        // Note: we prevent updating title and url name due to the uniqid
        // stored in url-name. Otherwise, permanent links would fail
        $dbArticle->setContent($article->getContent());
        $dbArticle->setIsPublished($article->getIsPublished());
        $dbArticle->setCategory($article->getCategory());
        $dbArticle->setAuthor($article->getAuthor());
        $dbArticle->setLanguage($article->getLanguage());
        $dbArticle->setLastEditDate($article->getLastEditDate());
        $dbArticle->setCreationDate($article->getCreationDate());

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

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

        $uniqid = uniqid();
        $article->setUrlName(sprintf('%s-%s', $article->getUrlName(), $uniqid));

        $this->em->persist($article);

        try {
            $this->em->flush();

            // creating the ACL
            $objectIdentity = ObjectIdentity::fromDomainObject($article);
            $acl = $this->aclProvider->createAcl($objectIdentity);

            // creating the security identity for the select access role
            $superAdminSid = new RoleSecurityIdentity('ROLE_SUPER_ADMIN');
            $adminSid = new RoleSecurityIdentity('ROLE_ADMIN');
            // TODO: set this correctly -> use role inheritance
            $selectedRoleSid = new RoleSecurityIdentity($article->getAllowedUserGroup()->first()->getRole());

            // grant all permissions for super admins
            $acl->insertObjectAce($superAdminSid, MaskBuilder::MASK_OWNER);
            // grant VIEW, EDIT, CREATE, DELETE, UNDELETE permissions to admins
            $acl->insertObjectAce($adminSid, MaskBuilder::MASK_OPERATOR);
            // grant selected role VIEW permissions
            $acl->insertObjectAce($selectedRoleSid, MaskBuilder::MASK_VIEW);
            $this->aclProvider->updateAcl($acl);

        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s"', $this->entityName));
        }

        return $article;
    }

}
