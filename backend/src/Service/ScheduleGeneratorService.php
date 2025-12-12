<?php

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ScheduleGeneratorService
{
    private string $aiServicePath;

    public function __construct(string $projectDir)
    {
        $this->aiServicePath = $projectDir . '/../ai_service';
    }

    public function generate(array $constraints): array
    {
        $scriptPath = $this->aiServicePath . '/schedule_generator.py';
        $jsonData = json_encode($constraints);

        // We assume valid python3 environment.
        $process = new Process(['python3', $scriptPath, $jsonData]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        return json_decode($output, true) ?? [];
    }
}
