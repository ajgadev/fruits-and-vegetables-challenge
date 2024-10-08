<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Enum\WeightUnit;

class FruitControllerTest extends WebTestCase
{
    public function testListEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fruits');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        // // Assert metadata
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('meta', $responseData);
        $this->assertArrayHasKey('total_items', $responseData['meta']);
        $this->assertArrayHasKey('total_pages', $responseData['meta']);
        $this->assertArrayHasKey('current_page', $responseData['meta']);
        $this->assertArrayHasKey('items_per_page', $responseData['meta']);

        // // Test unit presence in items
        foreach ($responseData['data'] as $fruit) {
            $this->assertArrayHasKey('weightUnit', $fruit);
        }

        // // Test search by name
        $client->request('GET', '/fruits?name=Apple');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        foreach ($responseData['data'] as $fruit) {
            $this->assertStringContainsString('Apple', $fruit['name']);
        }

        // // Test pagination
        $client->request('GET', '/fruits?page=1&limit=5');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        $this->assertCount(5, $responseData['data']);

        $client->request('GET', '/fruits?page=2&limit=5');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        $this->assertCount(5, $responseData['data']);

        // // Test unit conversion to kilograms
        $client->request('GET', '/fruits?unit=kg');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        foreach ($responseData['data'] as $fruit) {
            $this->assertLessThan(1000, $fruit['quantity']);
            $this->assertEquals(WeightUnit::KILOGRAM, $fruit['weightUnit']);
        }
    }

    public function testAddEndpoint(): void
    {
        $client = static::createClient();

        $data = [
            'name' => 'Mango',
            'quantity' => 5000
        ];

        $client->request(
            'POST',
            '/fruits',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Mango', $responseData['name']);
        $this->assertEquals(5000, $responseData['quantity']);
    }

    public function testRemoveEndpoint(): void
    {
        $client = static::createClient();

        // First, add a fruit to ensure one exists to remove
        $data = [
            'name' => 'Mango',
            'quantity' => 5000
        ];

        $client->request(
            'POST',
            '/fruits',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $fruitId = $responseData['id'];

        // Now test removing the fruit just added
        $client->request('DELETE', '/fruits/' . $fruitId);
        $this->assertResponseStatusCodeSame(200);

        // Ensure the fruit is no longer present
        $client->request('GET', '/fruits');
        $listData = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse(array_search($fruitId, array_column($listData, 'id')));
    }

    public function testAddFruitInvalidData()
    {
        $client = static::createClient();

        $data = [
            'name' => '',
            'quantity' => -50
        ];

        $client->request(
            'POST',
            '/fruits',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $response);
    }

    public function testListInvalidUnit()
    {
        $client = static::createClient();
        $client->request('GET', '/fruits?unit=invalid');
        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $response);
    }
}
