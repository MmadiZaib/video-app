<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="admin_main_page")
     */
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    /**
     * @Route("/categories", name="categories", methods={"GET", "POST"})
     */
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $isInvalid = null;

        if ($this->saveCategory($form, $category, $request)) {

            return $this->redirectToRoute('categories');

        } elseif($request->isMethod('POST')) {
            $isInvalid = 'is-invalid';
        }

        return $this->render('admin/categories.html.twig', [
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
            'categories' => $categories->getCategoryList($categories->buildTree()),
        ]);
    }

    /**
     * @Route("/edit-category/{id}", name="edit_category",  methods={"GET", "POST"})
     */
    public function editCategory(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $isInvalid = null;

        if ($this->saveCategory($form, $category, $request)) {

            return $this->redirectToRoute('categories');

        } elseif($request->isMethod('POST')) {
            $isInvalid = 'is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'is_invalid' => $isInvalid
        ]);
    }

    /**
     * @Route("/delete-category/{id}", name="delete_category")
     */
    public function deleteCategory(Category $category): Response
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('categories');
    }


    /**
     * @Route("/videos", name="videos")
     */
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    /**
     * @Route("/upload-video", name="upload_video")
     */
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/users", name="users")
     */
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, ?Category $editCategory = null): Response
    {
        $categories->getCategoryList($categories->buildTree());

        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editCategory' => $editCategory
        ]);
    }

    private function saveCategory(FormInterface $form, Category $category, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($request->request->get('category')['name']);

            $repository  = $this->getDoctrine()->getRepository(Category::class);
            $parent = $repository->find($request->request->get('category')['parent']);

            $category->setParent($parent);

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
