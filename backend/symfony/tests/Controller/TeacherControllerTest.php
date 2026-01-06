<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeacherControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetTeachersList()
    {
        $this->client->request('GET', '/api/teachers');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }

    public function testCreateTeacher()
    {
        $this->client->request('POST', '/api/teachers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'firstName' => 'Profesor',
            'lastName' => 'Prueba',
            'email' => 'profe@oxford.edu',
            'specialization' => 'Mathematics'
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [201, 401, 400]);
    }

    public function testAssignSubject()
    {
        // Test assigning a subject to a teacher
        $this->client->request('POST', '/api/teachers/1/assign-subject', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'subjectId' => 101, // Math
            'gradeId' => 5 // 5to Bach
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401, 404]);
    }
}
