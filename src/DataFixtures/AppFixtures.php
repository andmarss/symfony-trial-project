<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserPreferences;
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
    private const USERS = [
        [
            'username' => 'admin',
            'email'    => 'admin@mail.com',
            'password' => 'secret',
            'fullName' => 'Super Admin',
            'roles'    => [User::ROLE_ADMIN]
        ],
        [
            'username' => 'foo_bar',
            'email'    => 'foo_bar@mail.com',
            'password' => 'secret',
            'fullName' => 'Foo Bar',
            'roles'    => [User::ROLE_USER]
        ],
        [
            'username' => 'bar_baz',
            'email'    => 'bar_baz@mail.com',
            'password' => 'secret',
            'fullName' => 'Bar Baz',
            'roles'    => [User::ROLE_USER]
        ],
        [
            'username' => 'foo_baz',
            'email'    => 'foo_baz@mail.com',
            'password' => 'secret',
            'fullName' => 'Foo Baz',
            'roles'    => [User::ROLE_USER]
        ]
    ];

    private const POSTS = [
        'Hello, how are you',
        'It\'s nice sunny weather day',
        'I need to by some ice cream',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up today?',
        'Did you watch the game yesterday?',
        'How was you day?'
    ];

    private const LANGUAGES = [
        'en',
        'ru'
    ];
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
        $this->loadUsers($manager);

        $this->loadMicroPosts($manager);
    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < count(static::POSTS); $i++) {
            /**
             * @var MicroPost $microPost
             */
            $microPost = new MicroPost();
            /**
             * @var User $user
             */
            $user = $this->getReference(
                self::USERS[rand(0, count(self::USERS) -1)]['username']
            );

            $microPost->setText(
                self::POSTS[rand(0, count(self::POSTS) -1)]
            );
            $date = new \DateTime();
            $date->modify('-' . rand(0, 10) . ' day');
            $microPost->setTime($date);
            $microPost->setUser($user);

            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach (static::USERS as $userData) {
            /**
             * @var User $user
             */
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setFullName($userData['fullName']);
            $user->setEmail($userData['email']);
            $user->setPassword(
                $this->encoder->encodePassword($user, $userData['password'])
            );
            $user->setRoles($userData['roles']);
            $user->setEnabled(true);

            $this->addReference($userData['username'], $user);

            $preferences = new UserPreferences();
            $preferences->setLocale(static::LANGUAGES[rand(0, 1)]);

            $user->setPreferences($preferences);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
