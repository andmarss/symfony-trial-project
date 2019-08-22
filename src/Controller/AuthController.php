<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        return $this->render('auth/login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error'         => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     * @param string $token
     * @param UserRepository $repository
     * @return Response
     */
    public function confirm(string $token, UserRepository $repository)
    {
        /**
         * @var User $user
         */
        $user = $repository->findOneBy([
            'confirmationToken' => $token
        ]);

        if(!is_null($user)) {
            $user->setEnabled(true);
            $user->setConfirmationToken('');

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('confirmation/confirmation.html.twig', [
            'user' => $user
        ]);
    }
}