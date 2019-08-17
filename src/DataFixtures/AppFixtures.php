<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 *
 * @property UserPasswordEncoderInterface $encoder
 */
class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        //$this->loadMicroPosts($manager);

        $this->loadUsers($manager);
    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 2; $i++) {
            /**
             * @var MicroPost $microPost
             */
            $microPost = new MicroPost();

            $microPost->setText('Some random text ' . rand(0, 100));
            $microPost->setTime(new \DateTime());

            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        /**
         * @var User $user
         */
        $user = new User();
        $user->setUsername('foo_bar');
        $user->setFullName('Foo Bar');
        $user->setEmail('foo_bar@mail.com');
        $user->setPassword(
            $this->encoder->encodePassword($user, 'secret')
        );

        $manager->persist($user);
        $manager->flush();
    }
}
