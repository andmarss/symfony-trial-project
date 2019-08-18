<?php


namespace App\Security;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class MicroPostVoter
 * @package App\Security
 *
 * @property  AccessDecisionManagerInterface $manager
 */

class MicroPostVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    /**
     * @var AccessDecisionManagerInterface
     */
    private $manager;

    public function __construct(AccessDecisionManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if(!in_array($attribute, [static::EDIT, static::DELETE])) {
            return false;
        }

        if(!$subject instanceof MicroPost) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if($this->manager->decide($token, [User::ROLE_ADMIN])) return true;

        /**
         * @var User $authenticatedUser
         */
        $authenticatedUser = $token->getUser();

        if(!$authenticatedUser instanceof User) {
            return false;
        }
        /**
         * @var MicroPost $post
         */
        $post = $subject;

        return $post->getUser()->getId() === $authenticatedUser->getId();
    }
}