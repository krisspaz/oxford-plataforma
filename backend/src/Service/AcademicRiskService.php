<?php

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class AcademicRiskService
{
    private string $aiServicePath;

    public function __construct(string $projectDir)
    {
        $this->aiServicePath = $projectDir . '/../ai_service';
    }

    public function analyzeRisk(array $studentData): array
    {
        $scriptPath = $this->aiServicePath . '/risk_analysis.py';
        $jsonData = json_encode($studentData);

        // We assume valid python3 environment. In production, use full path or venv python.
        $process = new Process(['python3', $scriptPath, $jsonData]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        return json_decode($output, true) ?? [];
    }
}
