<?php

namespace App\Tests\unit\Utils;

use App\Twig\AppRuntime;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{

    protected $mockedCategoryTreeFrontPage;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlGenerator  = $kernel->getContainer()->get('router');

        $this->mockedCategoryTreeFrontPage = $this->getMockBuilder(CategoryTreeFrontPage::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $this->mockedCategoryTreeFrontPage->urlGenerator = $urlGenerator;

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
}
