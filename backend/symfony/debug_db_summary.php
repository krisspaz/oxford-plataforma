<?php

use App\Kernel;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Task;
use App\Entity\Attendance;
use App\Entity\Bimester;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

require_once dirname(__FILE__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    $kernel->boot();
    $container = $kernel->getContainer();
    $em = $container->get('doctrine')->getManager();

    echo "\n=== REPORTE DE ESTADO DE BASE DE DATOS ===\n";
    echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

    // Helper function
    $checkEntity = function($name, $class) use ($em) {
        $repo = $em->getRepository($class);
        $count = $repo->count([]);
        echo sprintf("[ %-15s ] Total: %d\n", $name, $count);
        
        if ($count > 0) {
            $last = $repo->findBy([], ['id' => 'DESC'], 3);
            foreach ($last as $item) {
                // Try to get a meaningful string representation
                $str = 'ID: ' . $item->getId();
                if (method_exists($item, 'getName')) $str .= ' - ' . $item->getName();
                elseif (method_exists($item, 'getTitle')) $str .= ' - ' . $item->getTitle();
                elseif (method_exists($item, 'getFullName')) $str .= ' - ' . $item->getFullName();
                elseif (method_exists($item, 'getFirstName')) $str .= ' - ' . $item->getFirstName() . ' ' . $item->getLastName();
                elseif (method_exists($item, 'getDate')) $str .= ' - ' . $item->getDate()->format('Y-m-d');
                
                echo "   -> $str\n";
            }
            echo "\n";
        } else {
            echo "   (Sin registros)\n\n";
        }
    };

    try {
        $checkEntity('Students', Student::class);
        $checkEntity('Teachers', Teacher::class);
        $checkEntity('Tasks', Task::class);
        $checkEntity('Attendance', Attendance::class);
        $checkEntity('Bimesters', Bimester::class);
        $checkEntity('Users', User::class);
    } catch (\Exception $e) {
        echo "\nERROR AL CONSULTAR BD: " . $e->getMessage() . "\n";
    }
};
