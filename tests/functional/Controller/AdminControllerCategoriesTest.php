<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{
    protected $client;

    /** @var EntityManagerInterface  */
    protected $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        //$this->entityManager->beginTransaction();
        //$this->entityManager->getConnection()->setAutoCommit(false);
    }


    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $this->assertCount(21, $crawler->filter('option'));
    }

    public function testNewCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');
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
        $crawler = $this->client->request('GET', '/admin/edit-category/1');
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
        $this->client->request('GET', '/admin/delete-category/1');
        $category = $this->entityManager->getRepository(Category::class)->find(1);

        $this->assertNull($category);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        //$this->entityManager->rollback();
        //$this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
