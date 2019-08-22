<?php


namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package App\Controller
 * @property NotificationRepository $repository
 *
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @var NotificationRepository
     */
    private $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * @Route("/unread-count", name="notification_unread")
     */
    public function unreadCount()
    {
        return new JsonResponse([
            'count' => $this->repository->findUnseenByUser(
                $this->getUser()
            )
        ]);
    }

    /**
     * @Route("/all", name="notification_all")
     */
    public function notifications()
    {
        return $this->render('notification/notifications.html.twig', [
            'notifications' => $this->repository->findBy([
                'seen' => false,
                'user' => $this->getUser()
            ])
        ]);
    }

    /**
     * @param Notification $notification
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/acknowledge/{id}", name="notification_acknowledge")
     */
    public function acknowledge(Notification $notification)
    {
        $notification->setSeen(true);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }

    /**
     * @Route("/acknowledge-all", name="notification_acknowledge_all")
     */
    public function acknowledgeAll()
    {
        $this->repository->markAllAsReadByUser($this->getUser());

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('notification_all');
    }
}