<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Servicio de integración con CorpoSistemas para Facturación Electrónica (FEL) SAT Guatemala
 */
class CorpoSistemasService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $apiUrl;
    private string $apiKey;
    private string $nitEmisor;
    
    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $corposistemasApiUrl,
        string $corposistemasApiKey,
        string $corposistemasNit
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiUrl = $corposistemasApiUrl;
        $this->apiKey = $corposistemasApiKey;
        $this->nitEmisor = $corposistemasNit;
    }

    /**
     * Emitir Factura Electrónica SAT
     */
    public function emitirFactura(array $datos): array
    {
        return $this->emitirDocumento('FACT', $datos);
    }

    /**
     * Emitir Recibo Electrónico SAT
     */
    public function emitirRecibo(array $datos): array
    {
        return $this->emitirDocumento('RECI', $datos);
    }

    /**
     * Emitir Nota de Crédito
     */
    public function emitirNotaCredito(array $datos): array
    {
        return $this->emitirDocumento('NCRE', $datos);
    }

    /**
     * Emitir documento electrónico genérico
     */
    private function emitirDocumento(string $tipo, array $datos): array
    {
        try {
            $payload = [
                'Tipo' => $tipo,
                'NITEmisor' => $this->nitEmisor,
                'NITReceptor' => $datos['nit'] ?? 'CF',
                'NombreReceptor' => $datos['nombre'] ?? 'Consumidor Final',
                'Items' => $this->formatearItems($datos['items'] ?? []),
                'Total' => $datos['total'] ?? 0,
                'FechaEmision' => (new \DateTime())->format('Y-m-d\TH:i:s'),
            ];

            $response = $this->httpClient->request('POST', $this->apiUrl . '/documentos/emitir', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = $response->toArray();

            $this->logger->info('Documento FEL emitido', [
                'tipo' => $tipo,
                'uuid' => $result['uuid'] ?? null,
                'serie' => $result['serie'] ?? null,
                'numero' => $result['numero'] ?? null,
            ]);

            return [
                'success' => true,
                'uuid' => $result['uuid'] ?? null,
                'serie' => $result['serie'] ?? null,
                'numero' => $result['numero'] ?? null,
                'fechaCertificacion' => $result['fechaCertificacion'] ?? null,
                'xmlUrl' => $result['xmlUrl'] ?? null,
                'pdfUrl' => $result['pdfUrl'] ?? null,
                'raw' => $result,
            ];

        } catch (\Exception $e) {
            $this->logger->error('Error emitiendo documento FEL', [
                'tipo' => $tipo,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Anular documento electrónico
     */
    public function anularDocumento(string $uuid, string $motivo): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->apiUrl . '/documentos/anular', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'UUID' => $uuid,
                    'NITEmisor' => $this->nitEmisor,
                    'MotivoAnulacion' => $motivo,
                    'FechaAnulacion' => (new \DateTime())->format('Y-m-d\TH:i:s'),
                ],
            ]);

            $result = $response->toArray();

            $this->logger->info('Documento FEL anulado', ['uuid' => $uuid]);

            return [
                'success' => true,
                'uuid' => $uuid,
                'fechaAnulacion' => $result['fechaAnulacion'] ?? null,
            ];

        } catch (\Exception $e) {
            $this->logger->error('Error anulando documento FEL', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Consultar estado de documento
     */
    public function consultarDocumento(string $uuid): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl . '/documentos/' . $uuid, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
            ]);

            return $response->toArray();

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Descargar PDF del documento
     */
    public function descargarPdf(string $uuid): ?string
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl . '/documentos/' . $uuid . '/pdf', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
            ]);

            return $response->getContent();

        } catch (\Exception $e) {
            $this->logger->error('Error descargando PDF', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Formatear items para el payload
     */
    private function formatearItems(array $items): array
    {
        return array_map(function ($item) {
            return [
                'Cantidad' => $item['cantidad'] ?? 1,
                'Descripcion' => $item['descripcion'] ?? '',
                'PrecioUnitario' => $item['precio'] ?? 0,
                'Total' => ($item['cantidad'] ?? 1) * ($item['precio'] ?? 0),
                'TipoProducto' => $item['tipo'] ?? 'S', // S = Servicio, B = Bien
            ];
        }, $items);
    }

    /**
     * Generar recibo interno (sin SAT)
     */
    public function generarReciboInterno(array $datos): array
    {
        // Los recibos internos no van a SAT, solo se generan localmente
        return [
            'success' => true,
            'tipo' => 'RECIBO_INTERNO',
            'numero' => $datos['numero'] ?? uniqid('RI-'),
            'fecha' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
    }
}
