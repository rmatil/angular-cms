<?php


namespace rmatil\CmsBundle\Security;


use rmatil\CmsBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

class RestrictableVoter extends Voter {

    const VIEW = 'VIEW';
    const EDIT = 'EDIT';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * RestrictableVoter constructor.
     *
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager, RoleHierarchyInterface $roleHierarchy) {
        $this->decisionManager = $decisionManager;
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject) {
        if ( ! in_array($attribute, [self::EDIT, self::VIEW])) {
            return false;
        }

        if ( ! $subject instanceof IRestrictable) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        // grant access if superadmin
        if ($this->decisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $token);
            case self::EDIT:
                return $this->canEdit($subject, $token);
            default:
                return false;
        }
    }

    /**
     * Returns true, if the token has the role required by subject
     *
     * @param IRestrictable  $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canView(IRestrictable $subject, TokenInterface $token) {
        return $this->checkRoles($subject, $token);
    }

    /**
     * Returns true, if the token has the role required by subject
     *
     * @param IRestrictable  $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function canEdit(IRestrictable $subject, TokenInterface $token) {
        return $this->checkRoles($subject, $token);
    }

    private function checkRoles(IRestrictable $subject, TokenInterface $token) {
        $requiredRole = $subject->getAllowedUserGroup();

        if (null === $requiredRole) {
            return true;
        }

        // we require that the user is logged in
        if ($token->getUser() instanceof User) {
            return false;
        }

        /** @var RoleInterface[] $presentRoles */
        $presentRoles = [];
        foreach ($token->getRoles() as $role) {
            $presentRoles[] = $role->getRole();
            $presentRoles = array_merge($presentRoles, $this->roleHierarchy->getReachableRoles([$role]));
        }

        $roleStrings = [];
        foreach ($presentRoles as $role) {
            $roleStrings[] = $role->getRole();
        }

        $roleStrings = array_unique($roleStrings);

        if (in_array($requiredRole, $roleStrings)) {
            return true;
        }

        return false;
    }
}
