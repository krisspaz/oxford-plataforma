<?php

namespace App\Message;

class GenerateReportMessage
{
    private string $type;
    private string $scope;
    private array $filters;

    public function __construct(string $type, string $scope, array $filters = [])
    {
        $this->type = $type;
        $this->scope = $scope;
        $this->filters = $filters;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
