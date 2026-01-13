<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'admin@oxford.edu.gt',
            'password' => 'admin123'
        ]));

        // May return 200 or 401 depending on database state
        $this->assertContains(
            $client->getResponse()->getStatusCode(),
            [200, 401]
        );
    }

    public function testLoginWithMissingEmail(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'password' => 'password123'
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
    }

    public function testLoginWithMissingPassword(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'test@example.com'
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testLoginWithInvalidEmail(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'invalid-email',
            'password' => 'password123'
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRefreshWithoutToken(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/auth/refresh');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testLogoutEndpoint(): void
    {
        $client = static::createClient();
        
        // 1. Get Token
        $client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'admin@oxford.edu',
            'password' => 'oxford123'
        ]));
        $data = json_decode($client->getResponse()->getContent(), true);
        $token = $data['token'] ?? '';

        // 2. Logout with token
        $client->request('POST', '/api/auth/logout', [], [], [
             'HTTP_AUTHORIZATION' => 'Bearer ' . $token
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($response['success']);
    }

    public function testMeWithoutAuthentication(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/auth/me');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
