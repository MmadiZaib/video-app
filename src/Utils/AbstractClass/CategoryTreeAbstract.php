<?php


namespace App\Utils\AbstractClass;


use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    /** @var string */
    public $categoryList;

    /**
     * @var array|null
     */
    public $categoriesArray;

    protected static $dbConnection;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var UrlGeneratorInterface
     */
    public $urlGenerator;


    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->categoriesArray = $this->getCategories();
    }

    abstract public function getCategoryList(array $categories_array);

    public function buildTree(?int $parentId = null): array
    {
        $subCategory = [];

        foreach ($this->categoriesArray as $category){
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($category['id']);
                if ($children){
                    $category['children'] = $children;
                }

                $subCategory[] = $category;
            }
        }

        return $subCategory;
    }

    /**
     * @return array|null
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function getCategories(): ?array
    {
        if (self::$dbConnection)
        {
            return self::$dbConnection;
        }

        $connection = $this->entityManager->getConnection();
        $sql = "select * from category";
        $statement = $connection->prepare($sql);
        $statement->execute();

        return self::$dbConnection = $statement->fetchAll();
    }
}