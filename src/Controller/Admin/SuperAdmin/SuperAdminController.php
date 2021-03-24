<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Form\CategoryType;
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
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/su/upload-video", name="upload_video")
     */
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }
}
