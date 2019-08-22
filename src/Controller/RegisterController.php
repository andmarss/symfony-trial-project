<?php


namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Form\UserType;
use App\Security\TokenGenerator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegisterController
 * @package App\Controller
 *
 * @property RouterInterface $router
 * @property TokenGenerator $generator
 */
class RegisterController extends AbstractController
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TokenGenerator
     */
    private $generator;

    /**
     * RegisterController constructor.
     * @param RouterInterface $router
     * @param TokenGenerator $generator
     */
    public function __construct(RouterInterface $router, TokenGenerator $generator)
    {
        $this->router = $router;
        $this->generator = $generator;
    }

    /**
     * @Route("/register", name="user_register")
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(UserPasswordEncoderInterface $encoder, Request $request, EventDispatcherInterface $dispatcher)
    {
        /**
         * @var User $user
         */
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword(
                $user,
                $user->getPlainPassword()
            );

            $user->setPassword($password);
            $user->setConfirmationToken($this->generator->generateToken());

            /**
             * @var ObjectManager $manager
             */
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $userRegisterEvent = new UserRegisterEvent($user);
            $dispatcher->dispatch($userRegisterEvent, UserRegisterEvent::NAME);

            return $this->redirect(
                $this->router->generate('micro_post_index')
            );
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}