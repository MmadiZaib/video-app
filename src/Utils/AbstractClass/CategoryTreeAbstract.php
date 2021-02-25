<?php


namespace App\Utils\AbstractClass;


use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var array|null
     */
    public $categoriesArray;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->categoriesArray = $this->getCategories();
    }

    abstract public function getCategoryList(array $categories_array);

    private function getCategories(): ?array
    {
        $query = $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')->getQuery();
        return $query->getArrayResult();
    }
}