<?php


namespace App\Utils;


use App\Utils\AbstractClass\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{

    public function getCategoryList(array $categories_array, int $repeat = 0)
    {
        foreach ($categories_array as $category) {
            $this->categoryList[] = ['name' => str_repeat("-", $repeat) . $category['name'], 'id' => $category['id']];

            if (!empty($category['children'])) {
                $repeat = $repeat + 2;
                $this->getCategoryList($category['children'], $repeat);
                $repeat = $repeat - 2;
            }
        }

        return $this->categoryList;
    }
}