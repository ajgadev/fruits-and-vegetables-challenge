<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StorageControllerTest extends WebTestCase
{
    public function testProcessRequestValidData()
    {
        $client = static::createClient();
        $validData = [
            ['type' => 'fruit', 'name' => 'Apple', 'quantity' => 500, 'unit' => 'g']
        ];

        $client->request(
            'POST',
            '/process',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($validData)
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Request processed', $response['status']);
    }

    public function testProcessRequestInvalidJson()
    {
        $client = static::createClient();
        $invalidJson = "{'type': 'fruit', 'name': 'Apple', 'quantity': 'invalid'}"; // Malformed JSON

        $client->request(
            'POST',
            '/process',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $invalidJson
        );

        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid JSON', $response['error']);
    }
}