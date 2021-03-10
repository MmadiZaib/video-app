<?php


namespace App\Utils;


use App\Twig\AppRuntime;
use App\Utils\AbstractClass\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{
    public $html_1 = '<ul>';
    public $html_2 = '<li>';
    public $html_3 = '<a href="';
    public $html_4 = '">';
    public $html_5 = '</a></li>';
    public $html_6 = '</ul>';

    /**
     * @var AppRuntime
     */
    public $slugger;

    /** @var string */
    public $mainParentName;

    /** @var integer */
    public $mainParentId;

    /** @var string */
    public $currentCategoryName;


    public function getCategoryList(array $categories_array): string
    {
        $this->categoryList .= $this->html_1;

        foreach ($categories_array as $category) {
            $categoryName = $this->slugger->slugify($category['name']);
            $url = $this->urlGenerator->generate('video_list', ['category_name' => $categoryName, 'id' => $category['id'] ]);
            $this->categoryList .= $this->html_2 . $this->html_3 . $url . $this->html_4 . $category['name'] . $this->html_5;

            if (!empty($category['children'])) {
                $this->getCategoryList($category['children']);
            }
        }

        $this->categoryList .= $this->html_6;

        return $this->categoryList;
    }

    public function getMainParent(int $id): array
    {
        $key = array_search($id, array_column($this->categoriesArray, 'id'));

        if($this->categoriesArray[$key]['parent_id'] !== null) {
            return $this->getMainParent($this->categoriesArray[$key]['parent_id']);
        }
        else {
            return [
                'id' =>  $this->categoriesArray[$key]['id'],
                'name' => $this->categoriesArray[$key]['name']
            ];
        }
    }

    public function getCategoryListAndParent(int $id): string
    {
        $this->slugger  = new AppRuntime();
        $parentData = $this->getMainParent($id); // main parent of subcategory

        $this->mainParentName = $parentData['name'];
        $this->mainParentId = $parentData['id'];

        $key = array_search($id, array_column($this->categoriesArray, 'id'));

        $this->currentCategoryName = $this->categoriesArray[$key]['name'];

        $categoriesArray = $this->buildTree($parentData['id']);

        return $this->getCategoryList($categoriesArray);
    }

    public function getChildIds(int $parent): array
    {
        static $ids = [];

        foreach ($this->categoriesArray as $category) {
            if ($category['parent_id'] === $parent){
                $ids[] = $category['id'] . ',';
                $this->getChildIds($category['id']);
            }
        }

        return $ids;
    }
}