<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BlogController
 * @package App\Controller
 *
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @var SessionInterface $session
     */
    private $session;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * BlogController constructor.
     * @param SessionInterface $session
     * @param RouterInterface $router
     */
    public function __construct(SessionInterface $session, RouterInterface $router)
    {
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $this->session->get('posts')
        ]);
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');

        $posts[uniqid()] = [
            'title' => 'Random title ' . rand(1, 50),
            'text'  => 'Random text ' . rand(1, 50)
        ];

        $this->session->set('posts', $posts);

        return new RedirectResponse($this->router->generate('blog_index'));
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     *
     * @param string $id
     * @return Response
     */
    public function show(string $id): Response
    {
        $posts = $this->session->get('posts');

        if(!$posts || !isset($posts[$id])) {
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render('blog/post.html.twig', [
            'id'    => $id,
            'post' => $posts[$id]
        ]);
    }
}