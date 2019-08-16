<?php


namespace App\Controller;

use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/micro-post")
 *
 * @property MicroPostRepository $repository
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private $repository;

    public function __construct(MicroPostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
        return $this->render('micro-post/index.html.twig', [
            'posts' => $this->repository->findAll()
        ]);
    }
}