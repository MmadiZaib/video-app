<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Category;
use App\Tests\utils\Rollback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{
    use Rollback;

    protected $client;

    /** @var EntityManagerInterface  */
    protected $entityManager;


    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');
        $this->assertCount(21, $crawler->filter('option'));
    }

    public function testNewCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');
        $form = $crawler->selectButton('Add')->form([
            'category[name]' => 'Other',
            'category[parent]' => 1,
        ]);

        $this->client->submit($form);
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Other']);

        $this->assertNotNull($category);
        $this->assertSame('Other', $category->getName());
    }

    public function testEditCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/edit-category/1');
        $form = $crawler->selectButton('Save')->form([
            'category[name]' => 'Electronics 2',
            'category[parent]' => 0,
        ]);

        $this->client->submit($form);
        $category = $this->entityManager->getRepository(Category::class)->find(1);

        $this->assertSame('Electronics 2', $category->getName());
    }

    public function testDeleteCategory(): void
    {
        $this->client->request('GET', '/admin/su/delete-category/1');
        $category = $this->entityManager->getRepository(Category::class)->find(1);

        $this->assertNull($category);
    }
}
