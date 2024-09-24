<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Enum\WeightUnit;

class VegetableControllerTest extends WebTestCase
{
    public function testListEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/vegetables');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        // Assert metadata
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('meta', $responseData);
        $this->assertArrayHasKey('total_items', $responseData['meta']);
        $this->assertArrayHasKey('total_pages', $responseData['meta']);
        $this->assertArrayHasKey('current_page', $responseData['meta']);
        $this->assertArrayHasKey('items_per_page', $responseData['meta']);

        // Test unit presence in items
        foreach ($responseData['data'] as $vegetable) {
            $this->assertArrayHasKey('weightUnit', $vegetable);
        }

        // Test search by name
        $client->request('GET', '/vegetables?name=Carrot');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        foreach ($responseData['data'] as $vegetable) {
            $this->assertStringContainsString('Carrot', $vegetable['name']);
        }

        // Test pagination
        $client->request('GET', '/vegetables?page=1&limit=5');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        $this->assertCount(5, $responseData['data']);

        $client->request('GET', '/vegetables?page=2&limit=5');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        $this->assertCount(5, $responseData['data']);

        // Test unit conversion to kilograms
        $client->request('GET', '/vegetables?unit=kg');
        $this->assertResponseIsSuccessful();
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        foreach ($responseData['data'] as $vegetable) {
            $this->assertLessThan(1000, $vegetable['quantity']); // assuming quantities > 1 kg are originally in grams
            $this->assertEquals(WeightUnit::KILOGRAM, $vegetable['weightUnit']);
        }
    }

    public function testAddEndpoint(): void
    {
        $client = static::createClient();

        $data = [
            'name' => 'Zucchini',
            'quantity' => 300
        ];

        $client->request(
            'POST',
            '/vegetables',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Zucchini', $responseData['name']);
        $this->assertEquals(300, $responseData['quantity']);
    }

    public function testRemoveEndpoint(): void
    {
        $client = static::createClient();

        // First, add a vegetable to ensure one exists to remove
        $data = [
            'name' => 'Zucchini',
            'quantity' => 300
        ];

        $client->request(
            'POST',
            '/vegetables',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $vegetableId = $responseData['id'];

        // Now test removing the vegetable just added
        $client->request('DELETE', '/vegetables/' . $vegetableId);
        $this->assertResponseStatusCodeSame(204);

        // Ensure the vegetable is no longer present
        $client->request('GET', '/vegetables');
        $listData = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse(array_search($vegetableId, array_column($listData, 'id')));
    }
}