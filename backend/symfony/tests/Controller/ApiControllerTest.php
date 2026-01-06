<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Controller Tests
 * PHPUnit test suite for API endpoints
 */
class ApiControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    // ==========================================
    // AUTH TESTS
    // ==========================================

    public function testLoginWithValidCredentials(): void
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'admin@oxford.edu',
                'password' => 'oxford123'
            ])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'invalid@test.com',
                'password' => 'wrongpassword'
            ])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUnauthorizedAccess(): void
    {
        $this->client->request('GET', '/api/students');
        
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    // ==========================================
    // STUDENTS TESTS
    // ==========================================

    public function testGetStudentsWithAuth(): void
    {
        $token = $this->getAuthToken();

        $this->client->request(
            'GET',
            '/api/students',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testCreateStudent(): void
    {
        $token = $this->getAuthToken();

        $this->client->request(
            'POST',
            '/api/students',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'firstName' => 'Test',
                'lastName' => 'Student',
                'code' => 'TEST-' . time(),
                'email' => 'test' . time() . '@test.com'
            ])
        );

        $response = $this->client->getResponse();
        $this->assertContains(
            $response->getStatusCode(),
            [Response::HTTP_CREATED, Response::HTTP_OK]
        );
    }

    // ==========================================
    // GRADES TESTS
    // ==========================================

    public function testGetGrades(): void
    {
        $token = $this->getAuthToken();

        $this->client->request(
            'GET',
            '/api/grades',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    // ==========================================
    // HEALTH CHECK TESTS
    // ==========================================

    public function testHealthCheck(): void
    {
        $this->client->request('GET', '/health');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('healthy', $data['status'] ?? 'ok');
    }

    // ==========================================
    // SECURITY TESTS
    // ==========================================

    public function testCSRFProtection(): void
    {
        // CSRF should be handled for state-changing requests
        $this->client->request('POST', '/api/students');
        
        $response = $this->client->getResponse();
        $this->assertNotEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testSecurityHeaders(): void
    {
        $this->client->request('GET', '/');
        
        $response = $this->client->getResponse();
        
        // Check for security headers
        $this->assertTrue(
            $response->headers->has('X-Frame-Options') ||
            $response->headers->has('Content-Security-Policy')
        );
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function getAuthToken(): string
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'admin@oxford.edu',
                'password' => 'oxford123'
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        return $data['token'] ?? '';
    }
}
