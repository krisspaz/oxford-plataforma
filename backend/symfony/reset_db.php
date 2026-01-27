<?php

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;

require_once dirname(__FILE__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    $kernel->boot();
    $container = $kernel->getContainer();
    $em = $container->get('doctrine')->getManager();
    $connection = $em->getConnection();

    echo "\n=== REINICIO DE TABLAS ACADÉMICAS (PostgreSQL) ===\n\n";
    
    try {
        $tables = [
            'attendance',
            'grade_entry',
            'task_submission',
            'task', 
            'bimester',
        ];

        foreach ($tables as $table) {
            echo "Vaciar tabla: $table... ";
            try {
                // CASCADE is required to truncate tables with foreign key references in Postgres
                $connection->executeQuery("TRUNCATE TABLE $table CASCADE");
                echo "OK\n";
            } catch (\Exception $e) {
                 if (strpos($e->getMessage(), 'does not exist') !== false) {
                     echo "Skip (Tabla no existe)\n";
                 } else {
                     echo "Error: " . $e->getMessage() . "\n";
                 }
            }
        }
        
        echo "\nTodo listo. Datos académicos eliminados. Usuarios/Docentes/Estudiantes conservados.\n";

    } catch (\Exception $e) {
        echo "\nERROR CRÍTICO: " . $e->getMessage() . "\n";
    }
};
