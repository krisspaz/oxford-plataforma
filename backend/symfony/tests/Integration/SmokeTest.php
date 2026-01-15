<?php

namespace App\Tests\Integration;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Entity\Teacher;

class SmokeTest extends ApiTestCase
{
    private $client;
    private $token;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    private function getAuthToken(): string
    {
        if ($this->token) {
            return $this->token;
        }

        // 1. Create a test user if not exists (or use fixtures)
        // ideally we rely on fixtures, but for smoke test robustness we can use the default admin
        
        $response = $this->client->request('POST', '/api/login_check', [
            'json' => [
                'email' => 'admin@oxford.edu',
                'password' => $_ENV['APP_DEFAULT_PASSWORD'] ?? 'oxford123'
            ],
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->token = $data['token'];

        return $this->token;
    }

    public function testLoginIsSuccessful(): void
    {
        $this->getAuthToken();
        $this->assertResponseIsSuccessful();
    }

    public function testPublicDocsAreUp(): void
    {
        $this->client->request('GET', '/api/docs');
        $this->assertResponseIsSuccessful();
    }

    public function testProtectedEndpointStudents(): void
    {
        $token = $this->getAuthToken();
        $this->client->request('GET', '/api/students', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        
        // Use 200 or 404 (if empty) but not 401/500
        $this->assertResponseIsSuccessful(); 
    }

    public function testProtectedEndpointTeachers(): void
    {
        $token = $this->getAuthToken();
        $this->client->request('GET', '/api/teachers', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        
        $this->assertResponseIsSuccessful();
    }

    public function testProtectedEndpointDashboardStats(): void
    {
        $token = $this->getAuthToken();
        $this->client->request('GET', '/api/dashboard/stats', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        
        $this->assertResponseIsSuccessful();
    }
}
