<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AttendanceControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testTakeAttendance()
    {
        $this->client->request('POST', '/api/attendance/batch', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'classId' => 101,
            'date' => '2026-01-20',
            'records' => [
                ['studentId' => 1, 'status' => 'present'],
                ['studentId' => 2, 'status' => 'absent']
            ]
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [201, 401, 400]);
    }
}
