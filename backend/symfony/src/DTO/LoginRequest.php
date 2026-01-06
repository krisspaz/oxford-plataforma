<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest
{
    #[Assert\NotBlank(message: 'Email es requerido')]
    #[Assert\Email(message: 'Email inválido')]
    public string $email;
    
    #[Assert\NotBlank(message: 'Contraseña es requerida')]
    #[Assert\Length(min: 6, minMessage: 'Contraseña muy corta')]
    public string $password;
    
    public ?string $twoFactorCode = null;
}
