<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class CategoriesController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, ?Category $editCategory = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories->getCategoryList($categories->buildTree());

        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editCategory' => $editCategory,
        ]);
    }

    /**
     * @Route("/su/delete-category/{id}", name="delete_category")
     */
    public function deleteCategory(Category $category): Response
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('categories');
    }
}
