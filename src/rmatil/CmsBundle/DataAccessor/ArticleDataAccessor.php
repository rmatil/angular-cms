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
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleDataAccessor extends DataAccessor {

    use UpdateUserGroupTrait;

    protected $articleMapper;

    public function __construct(EntityManagerInterface $em, ArticleMapper $articleMapper, MutableAclProviderInterface $aclProvider, TokenStorageInterface $tokenStorage, LoggerInterface $logger) {
        parent::__construct(EntityNames::ARTICLE, $em, $aclProvider, $tokenStorage, $logger);

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

        // Note: we prevent updating title and url name due to the uniqid
        // stored in url-name. Otherwise, permanent links would fail
        $dbArticle->setContent($article->getContent());
        $dbArticle->setIsPublished($article->getIsPublished());
        $dbArticle->setCategory($article->getCategory());
        $dbArticle->setAuthor($article->getAuthor());
        $dbArticle->setLanguage($article->getLanguage());
        $dbArticle->setCreationDate($article->getCreationDate());
        $dbArticle->setLastEditDate(new DateTime('now', new DateTimeZone('UTC')));

        // only update ACL if allowed role changed
        if ($article->getAllowedUserGroup()->getRole() !== $dbArticle->getAllowedUserGroup()->getRole()) {
            // since the exists already, we can update its ACL before flush
            // 1st remove old ACL
            $objectIdentity = ObjectIdentity::fromDomainObject($article);
            /** @var Acl $acl */
            $acl = $this->aclProvider->findAcl($objectIdentity);

            // build the old securityIdentity and remove it
            $oldSid = new RoleSecurityIdentity($dbArticle->getAllowedUserGroup()->getRole());
            foreach ($acl->getObjectAces() as $idx => $ace) {
                // remove ACE if not for admin and super admin
                /** @var EntryInterface $ace */
                if ($ace->getSecurityIdentity()->equals($oldSid) &&
                    ! in_array($dbArticle->getAllowedUserGroup()->getRole(), ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])) {
                    // remove ACE
                    $acl->deleteObjectAce($idx);
                }
            }

            // grant selected role VIEW permissions if not already specified
            if ( ! in_array($article->getAllowedUserGroup()->getRole(), ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])) {
                $selectedRoleSid = new RoleSecurityIdentity($article->getAllowedUserGroup()->getRole());
                $acl->insertObjectAce($selectedRoleSid, MaskBuilder::MASK_VIEW);
            }

            $this->aclProvider->updateAcl($acl);

            // also update allowed user group
            $dbArticle->setAllowedUserGroup(
                $this->em->getRepository(EntityNames::USER_GROUP)->find($article->getAllowedUserGroup()->getId())
            );
        }


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

        $article->setAllowedUserGroup(
            $this->em->getRepository(EntityNames::USER_GROUP)->find($article->getAllowedUserGroup()->getId())
        );

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

            // grant all permissions for super admins
            $acl->insertObjectAce($superAdminSid, MaskBuilder::MASK_OWNER);
            // grant VIEW, EDIT, CREATE, DELETE, UNDELETE permissions to admins
            $acl->insertObjectAce($adminSid, MaskBuilder::MASK_OPERATOR);

            // grant selected role VIEW permissions if not already specified
            if ( ! in_array($article->getAllowedUserGroup()->getRole(), ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])) {
                $selectedRoleSid = new RoleSecurityIdentity($article->getAllowedUserGroup()->getRole());
                $acl->insertObjectAce($selectedRoleSid, MaskBuilder::MASK_VIEW);
            }

            $this->aclProvider->updateAcl($acl);

        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s"', $this->entityName));
        }

        return $article;
    }

}
