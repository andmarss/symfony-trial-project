<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegisterController
 * @package App\Controller
 *
 * @property RouterInterface $router
 */
class RegisterController extends AbstractController
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {

        $this->router = $router;
    }

    /**
     * @Route("/register", name="user_register")
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(UserPasswordEncoderInterface $encoder, Request $request)
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
            /**
             * @var ObjectManager $manager
             */
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirect(
                $this->router->generate('micro_post_index')
            );
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}