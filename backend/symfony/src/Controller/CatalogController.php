<?php
/**
 * CatalogController - Sistema Oxford
 * ===================================
 * ONLY for simple lookup catalogs that don't need full CRUD.
 * 
 * For entities with API Platform (grades, sections, subjects, teachers, academic_levels):
 * Use /api/{entity} endpoints directly - they are managed by API Platform.
 * 
 * This controller provides static lookup data for:
 * - Payment Methods
 * - Document Types
 * - Relationship Types
 * - Status Types
 * 
 * TODO: Migrate these to a Catalog entity for editability without deploy.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalogs')]
class CatalogController extends AbstractController
{
    /**
     * Payment Methods - Static lookup
     * These rarely change and are safe as static data.
     */
    #[Route('/payment-methods', methods: ['GET'])]
    public function paymentMethods(): JsonResponse
    {
        $methods = [
            ['id' => 1, 'code' => 'EFECTIVO', 'name' => 'Efectivo'],
            ['id' => 2, 'code' => 'TARJETA_CREDITO', 'name' => 'Tarjeta de Crédito'],
            ['id' => 3, 'code' => 'TARJETA_DEBITO', 'name' => 'Tarjeta de Débito'],
            ['id' => 4, 'code' => 'DEPOSITO', 'name' => 'Depósito Bancario'],
            ['id' => 5, 'code' => 'TRANSFERENCIA', 'name' => 'Transferencia'],
        ];
        
        return $this->json(['success' => true, 'data' => $methods]);
    }

    /**
     * Document Types - Static lookup
     */
    #[Route('/document-types', methods: ['GET'])]
    public function documentTypes(): JsonResponse
    {
        $types = [
            ['id' => 1, 'code' => 'DPI', 'name' => 'DPI'],
            ['id' => 2, 'code' => 'CUI', 'name' => 'CUI Menor'],
            ['id' => 3, 'code' => 'PASAPORTE', 'name' => 'Pasaporte'],
            ['id' => 4, 'code' => 'CERT_NAC', 'name' => 'Certificado de Nacimiento'],
        ];
        
        return $this->json(['success' => true, 'data' => $types]);
    }

    /**
     * Relationship Types - Static lookup
     */
    #[Route('/relationships', methods: ['GET'])]
    public function relationships(): JsonResponse
    {
        $relationships = [
            ['id' => 1, 'code' => 'PADRE', 'name' => 'Padre'],
            ['id' => 2, 'code' => 'MADRE', 'name' => 'Madre'],
            ['id' => 3, 'code' => 'ABUELO', 'name' => 'Abuelo/a'],
            ['id' => 4, 'code' => 'TIO', 'name' => 'Tío/a'],
            ['id' => 5, 'code' => 'HERMANO', 'name' => 'Hermano/a'],
            ['id' => 6, 'code' => 'OTRO', 'name' => 'Otro'],
        ];
        
        return $this->json(['success' => true, 'data' => $relationships]);
    }

    /**
     * Status Types - Static lookup
     */
    #[Route('/statuses', methods: ['GET'])]
    public function statuses(): JsonResponse
    {
        $statuses = [
            ['id' => 1, 'code' => 'ACTIVO', 'name' => 'Activo'],
            ['id' => 2, 'code' => 'INACTIVO', 'name' => 'Inactivo'],
            ['id' => 3, 'code' => 'PENDIENTE', 'name' => 'Pendiente'],
            ['id' => 4, 'code' => 'CANCELADO', 'name' => 'Cancelado'],
            ['id' => 5, 'code' => 'CERRADO', 'name' => 'Cerrado'],
        ];
        
        return $this->json(['success' => true, 'data' => $statuses]);
    }
}
