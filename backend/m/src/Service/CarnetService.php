<?php

namespace App\Service;

use App\Entity\Student;
use App\Entity\Enrollment;

class CarnetService
{
    private string $format = 'YYYY-NNNN'; // Year-Sequence

    /**
     * Generate a unique carnet number for a student
     */
    public function generateCarnet(int $year, int $sequence): string
    {
        return sprintf('%d-%04d', $year, $sequence);
    }

    /**
     * Generate carnet data for ID card
     */
    public function generateCarnetData(Student $student, Enrollment $enrollment): array
    {
        return [
            'carnet' => $student->getCarnet(),
            'fullName' => $student->getFirstName() . ' ' . $student->getLastName(),
            'grade' => $enrollment->getGrade()?->getName(),
            'section' => $enrollment->getSection()?->getName(),
            'year' => $enrollment->getSchoolCycle()?->getName() ?? date('Y'),
            'bloodType' => $student->getBloodType() ?? 'N/A',
            'emergencyContact' => $this->getEmergencyContact($student),
            'emergencyPhone' => $this->getEmergencyPhone($student),
            'barcode' => $this->generateBarcode($student->getCarnet()),
            'photoUrl' => '/uploads/student/' . $student->getId() . '/photo.jpg',
        ];
    }

    /**
     * Generate barcode data for carnet
     */
    private function generateBarcode(string $carnet): string
    {
        // Return a code128 compatible string
        return '*' . str_replace('-', '', $carnet) . '*';
    }

    private function getEmergencyContact(Student $student): string
    {
        // Get from guardian relationship
        return 'N/A';
    }

    private function getEmergencyPhone(Student $student): string
    {
        return 'N/A';
    }

    /**
     * Validate carnet format
     */
    public function validateCarnet(string $carnet): bool
    {
        return preg_match('/^\d{4}-\d{4}$/', $carnet) === 1;
    }
}
