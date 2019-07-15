<?php

namespace App\Tests;

use App\DataFixtures\ProductFixtures;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends AbstractControllerTest
{
    public function testListProducts()
    {
        $this->loadFixture(new ProductFixtures());
        $this->client->request('GET', '/products/');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($response->getContent(), json_encode([
            ['id' => 1, 'name' => 'BMW'],
            ['id' => 2, 'name' => 'Mercedes'],
            ['id' => 3, 'name' => 'Tesla'],
        ]));
    }

    public function testSingleProduct()
    {
        $this->loadFixture(new ProductFixtures());
        $this->client->request('GET', '/products/1');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($response->getContent(), json_encode(
            ['id' => 1, 'name' => 'BMW']
        ));
    }

    public function testSingleProductNotFound()
    {
        $this->client->request('GET', '/products/1');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCreateProduct()
    {
        $this->loadFixture(new ProductFixtures());
        $productName = 'Jaguar';
        $this->client->request('POST', '/products/', [], [], [], json_encode([
            'name' => $productName
        ]));
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        /** @var EntityManager $em */
        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        /** @var Product $product */
        $product = $em->getRepository(Product::class)->find(4);
        $this->assertEquals($productName, $product->getName());
    }

    public function testDeleteProduct()
    {
        $this->loadFixture(new ProductFixtures());
        $this->client->request('DELETE', '/products/1');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty($response->getContent());

        /** @var EntityManager $em */
        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        /** @var Product $product */
        $products = $em->getRepository(Product::class)->findAll();
        $this->assertCount(2, $products);
    }
}