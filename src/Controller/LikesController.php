<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LikesController
 * @package App\Controller
 *
 * @Route("/likes")
 */
class LikesController extends AbstractController
{
    /**
     * @param MicroPost $post
     * @Route("/like/{id}", name="likes_like")
     * @return JsonResponse
     */
    public function like(MicroPost $post)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if(!$user instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $post->like($user);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $post->getLikedBy()->count()
        ]);
    }

    /**
     * @param MicroPost $post
     * @Route("/unlike/{id}", name="likes_unlike")
     * @return JsonResponse
     */
    public function unlike(MicroPost $post)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if(!$user instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $post->unlike($user);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $post->getLikedBy()->count()
        ]);
    }
}