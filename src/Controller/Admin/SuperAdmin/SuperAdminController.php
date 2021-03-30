<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CategoryType;
use App\Form\VideoType;
use App\Repository\UserRepository;
use App\Services\Uploader\UploaderInterface;
use App\Utils\CategoryTreeAdminList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class SuperAdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/su/categories", name="categories", methods={"GET", "POST"})
     */
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $isInvalid = null;

        if ($this->saveCategory($form, $category, $request)) {
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = 'is-invalid';
        }

        return $this->render('admin/categories.html.twig', [
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
            'categories' => $categories->getCategoryList($categories->buildTree()),
        ]);
    }

    private function saveCategory(FormInterface $form, Category $category, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($request->request->get('category')['name']);

            $repository = $this->getDoctrine()->getRepository(Category::class);
            $parent = $repository->find($request->request->get('category')['parent']);

            $category->setParent($parent);

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @Route("/su/edit-category/{id}", name="edit_category",  methods={"GET", "POST"})
     */
    public function editCategory(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $isInvalid = null;

        if ($this->saveCategory($form, $category, $request)) {
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = 'is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'is_invalid' => $isInvalid,
        ]);
    }

    /**
     * @Route("/su/users", name="users")
     */
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/delete-user/{user}", name="delete_user")
     */
    public function deleteUser(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/su/upload-video-locally", name="upload_video_locally")
     */
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader): Response
    {
        $video = new Video();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $file = $video->getUploadVideo();

            $fileName = $fileUploader->upload($file);

            $basePath = Video::uploadFolder;
            $video->setPath(sprintf('%s%s', $basePath, $fileName[0]));
            $video->setTitle($fileName[1]);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }

        return $this->render('admin/upload_video_locally.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/su/delete-video/{video}/{path}", name="delete_video", requirements={"path"=".+"})
     */
    public function deleteVideo(Video $video, string $path, UploaderInterface $fileUploader): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();

        if ($fileUploader->delete($path)) {
            $this->addFlash('success', 'The video was successfully deleted.');
        } else {
            $this->addFlash('danger', 'We were not able to delete. Check the video.');
        }

        return $this->redirectToRoute('videos');
    }
}
