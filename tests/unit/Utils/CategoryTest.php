<?php

namespace App\Tests\unit\Utils;

use App\Twig\AppRuntime;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{

    protected $mockedCategoryTreeFrontPage;

    protected $mockedCategoryTreeAdminList;

    protected $mockedCategoryTreeAdminOptionList;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlGenerator  = $kernel->getContainer()->get('router');


        $testedClasses = [
            'CategoryTreeAdminList',
            'CategoryTreeAdminOptionList',
            'CategoryTreeFrontPage'

        ];

        foreach ($testedClasses as $class) {
            $name = 'mocked' . $class;
            $this->$name = $this->getMockBuilder('App\\Utils\\' . $class)
                ->disableOriginalConstructor()
                ->setMethods()
                ->getMock();
            $this->$name->urlGenerator = $urlGenerator;
        }

    }


    /** @dataProvider dataForCategoryTreeFrontPage */
    public function testCategoryTreeFrontPage(string $string, array $array, int $id): void
    {
        $this->mockedCategoryTreeFrontPage->categoriesArray = $array;
        $this->mockedCategoryTreeFrontPage->slugger = new AppRuntime;

        $mainParentId = $this->mockedCategoryTreeFrontPage->getMainParent($id)['id'];
        $array = $this->mockedCategoryTreeFrontPage->buildTree($mainParentId);

        $this->assertSame($string, $this->mockedCategoryTreeFrontPage->getCategoryList($array));
    }

    /** @dataProvider  dataForCategoryTreeAdminOptionList */
    public function testCategoryTreeAdminOptionList(array $arrayToCompare, $arrayFromDb): void
    {
        $this->mockedCategoryTreeAdminOptionList->categoriesArray = $arrayFromDb;
        $array= $this->mockedCategoryTreeAdminOptionList->buildTree();

        $this->assertSame($arrayToCompare, $this->mockedCategoryTreeAdminOptionList->getCategoryList($array));
    }

    /** @dataProvider dataForCategoryTreeAdminList */
    public function testCategoryTreeAdminList(string $string, array $arrayFromDb): void
    {
        $this->mockedCategoryTreeAdminList->categoriesArray = $arrayFromDb;
        $array = $this->mockedCategoryTreeAdminList->buildTree();

        $this->assertSame($string, $this->mockedCategoryTreeAdminList->getCategoryList($array));
    }



    public function dataForCategoryTreeFrontPage(): iterable
    {
        yield [
            '<ul><li><a href="/video-list/category/comp,26">comp</a></li><ul><li><a href="/video-list/category/lap,27">Lap</a></li><ul><li><a href="/video-list/category/hpp,28">hpp</a></li></ul></ul></ul>',
            [
                ["id" => 25, "parent_id" => null, "name" => "Electro"],
                ["id" => 26, "parent_id" => 25, "name" => "comp"],
                ["id" => 27, "parent_id" => 26, "name" => "Lap"],
                ["id" => 28, "parent_id" => 27, "name" => "hpp"]
            ],
            1
        ];
    }

    public function dataForCategoryTreeAdminOptionList(): iterable
    {
        yield [
            [
                ["name" => "Electro", "id" => 25],
                ["name" => "--comp", "id" => 26],
                ["name" => "----Lap", "id" => 27],
                ["name" => "------hpp", "id" => 28],
            ],
            [
                ["id" => 25, "parent_id" => null, "name" => "Electro"],
                ["id" => 26, "parent_id" => 25, "name" => "comp"],
                ["id" => 27, "parent_id" => 26, "name" => "Lap"],
                ["id" => 28, "parent_id" => 27, "name" => "hpp"]
            ],
        ];
    }


    public function dataForCategoryTreeAdminList(): iterable
    {
        yield [
          '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i> Electronics<a href="/admin/edit-category/1">Edit</a><a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/1">Delete</a></li></ul>',
          [
              ['id' => 1, 'parent_id' => null, 'name' => 'Electronics']
          ]
        ];
    }
}
