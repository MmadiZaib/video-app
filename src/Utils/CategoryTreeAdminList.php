<?php


namespace App\Utils;


use App\Utils\AbstractClass\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{
    public $html_1 = '<ul class="fa-ul text-left">';
    public $html_2 = '<li><i class="fa-li fa fa-arrow-right"></i> ';
    public $html_3 = '<a href="';
    public $html_4 = '">';
    public $html_5 = '</a><a onclick="return confirm(\'Are you sure?\');" href="';
    public $html_6 = '">';
    public $html_7 = '</a></li>';
    public $html_8 = '</ul>';


    public function getCategoryList(array $categories_array)
    {
        $this->categoryList .= $this->html_1;

        foreach ($categories_array as $category) {
            $editUrl = $this->urlGenerator->generate('edit_category', ['id' => $category['id']]);
            $deleteUrl = $this->urlGenerator->generate('delete_category', ['id' => $category['id']]);

            $this->categoryList .= $this->html_2 . $category['name'] .
                $this->html_3 . $editUrl .
                $this->html_4 . 'Edit' .
                $this->html_5 . $deleteUrl . $this->html_6 . 'Delete' . $this->html_7;

            if (!empty($category['children'])) {
                $this->getCategoryList($category['children']);
            }
        }

        $this->categoryList .= $this->html_8;

        return $this->categoryList;
    }
}