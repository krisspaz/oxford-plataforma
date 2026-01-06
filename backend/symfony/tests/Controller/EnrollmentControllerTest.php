<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EnrollmentControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testEnrollStudent()
    {
        $this->client->request('POST', '/api/enrollments', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'studentId' => 1,
            'gradeId' => 5,
            'year' => 2026
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [201, 401]);
    }
}
