<?php

namespace App\DTO;

class ValidationErrorResponse
{
    public function __construct(
        public string $error = 'validation_error',
        public string $message = 'Datos inválidos',
        public array $errors = [],
    ) {}
}
