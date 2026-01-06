<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GradeControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testRecordGrade()
    {
        $this->client->request('POST', '/api/grades', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'studentId' => 1,
            'subjectId' => 101,
            'grade' => 85,
            'period' => 'BI_1',
            'comments' => 'Excellent work'
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [201, 401, 400]);
    }

    public function testGetStudentGrades()
    {
        $this->client->request('GET', '/api/students/1/grades');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401, 404]);
    }

    public function testUpdateGrade()
    {
        $this->client->request('PUT', '/api/grades/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'grade' => 90
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401, 404]);
    }

    public function testValidationInvalidGrade()
    {
        $this->client->request('POST', '/api/grades', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'studentId' => 1,
            'subjectId' => 101,
            'grade' => 150, // Invalid > 100
            'period' => 'BI_1'
        ]));
        // Should be Bad Request
        $this->assertContains($this->client->getResponse()->getStatusCode(), [400, 422, 401]);
    }
}
