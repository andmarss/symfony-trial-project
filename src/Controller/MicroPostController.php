<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;

/**
 * @Route("/micro-post")
 *
 * @property MicroPostRepository $repository
 * @property FormFactoryInterface $formFactory
 * @property EntityManagerInterface $entityManager
 * @property RouterInterface $router
 * @property FlashBagInterface $flashBag
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private $repository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        MicroPostRepository $repository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        FlashBagInterface $flashBag
    )
    {
        $this->repository = $repository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/", name="micro_post_index")
     * @param UserRepository $repository
     * @return Response
     */
    public function index(UserRepository $repository)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $usersToFollow = [];

        if($user instanceof User) {
            $posts = $this->repository->findAllByUsers($user->getFollowing());

            $usersToFollow = count($posts) === 0 ? $repository->findAllWithMoreThanZeroPosts($user) : [];
        } else {
            $posts = $this->repository->findBy([], ['time' => 'DESC']);
        }

        return $this->render('micro-post/index.html.twig', [
            'posts'         => $posts,
            'usersToFollow' => $usersToFollow
        ]);
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('delete', post)", message="Доступ запрещен")
     *
     * @param MicroPost $post
     * @return Response
     */
    public function delete(MicroPost $post): Response
    {
        // $this->denyAccessUnlessGranted('delete', $post);

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Микропост был успешно удален');

        return new RedirectResponse(
            $this->router->generate('micro_post_index')
        );
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request)
    {
        /**
         * @var MicroPost $microPost
         */
        $microPost = new MicroPost();
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $microPost->setUser($user);

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse(
                $this->router->generate('micro_post_index')
            );
        }

        return $this->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     * @param User $user
     * @return Response
     */
    public function userPosts(User $user)
    {
        return $this->render('micro-post/user-posts.html.twig', [
            'posts' => $user->getPosts(),
            'user'  => $user
        ]);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('edit', post)", message="Доступ запрещен")
     *
     * @param MicroPost $post
     * @param Request $request
     * @return Response
     */
    public function edit(MicroPost $post, Request $request): Response
    {
        // $this->denyAccessUnlessGranted('delete', $post);

        $form = $this->formFactory->create(MicroPostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return new RedirectResponse(
                $this->router->generate('micro_post_index')
            );
        }

        return $this->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="micro_post_post")
     *
     * @param MicroPost $post
     * @return Response
     */
    public function post(MicroPost $post): Response
    {
        return $this->render(
            'micro-post/post.html.twig',
            [
                'post' => $post
            ]
        );
    }
}