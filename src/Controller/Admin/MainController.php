<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, [
            'user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setName($form->get('name')->getData());
            $user->setLastName($form->get('last_name')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/videos", name="videos")
     */
    public function videos(CategoryTreeAdminOptionList $categories): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->getDoctrine()->getRepository(Video::class)->findBy([], ['title' => 'ASC']);

            $categories->getCategoryList($categories->buildTree());
        } else {
            $categories = null;
            $videos = $this->getUser()->getLikedVideos();
        }

        return $this->render('admin/videos.html.twig', [
            'videos' => $videos,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/cancel-plan", name="cancel_plan")
     */
    public function cancelPlan(): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());

        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \DateTime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($subscription);

        $em->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route("/delete-account", name="delete_account")
     */
    public function deleteAccount(SessionInterface $session): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser());

        $em->remove($user);
        $em->flush();

        $session->invalidate();

        return $this->redirectToRoute('main_page');
    }
}
