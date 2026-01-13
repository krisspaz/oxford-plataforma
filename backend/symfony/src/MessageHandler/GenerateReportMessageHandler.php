<?php

namespace App\MessageHandler;

use App\Message\GenerateReportMessage;
use App\Service\PdfService;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[AsMessageHandler]
class GenerateReportMessageHandler
{
    private PdfService $pdfService;
    private FilesystemOperator $storage;
    private LoggerInterface $logger;
    private HubInterface $hub;

    public function __construct(
        PdfService $pdfService,
        FilesystemOperator $oxfordStorage, 
        LoggerInterface $logger,
        HubInterface $hub
    ) {
        $this->pdfService = $pdfService;
        $this->storage = $oxfordStorage;
        $this->logger = $logger;
        $this->hub = $hub;
    }

    public function __invoke(GenerateReportMessage $message)
    {
        $this->logger->info('Starting report generation', [
            'type' => $message->getType(),
            'scope' => $message->getScope()
        ]);

        // 1. Simulate Data Gathering (In real app, query Repo based on filters)
        // For demonstration, we assume we are generating a generic report
        
        try {
            // 2. Generate PDF Content
            // We'll use a dummy wrapper or modify PdfService to accept raw data if needed.
            // For now, let's create a simple PDF using the service if possible, or just a dummy string for concept.
            // Since PdfService requires Entities, and we don't have them here easily without querying, 
            // we will create a text file as a placeholder or use a simplified method.
            
            // NOTE: In a real implementation we would fetch Student entities here.
            $content = "Reporte Generado: " . $message->getType() . "\nGenerated At: " . date('Y-m-d H:i:s');
            
            // 3. Save to MinIO
            $filename = strtolower($message->getType()) . '_' . uniqid() . '.txt'; // Using .txt for safety until PdfService is wired fully
            $path = 'reports/' . $filename;
            
            $this->storage->write($path, $content);
            
            $this->logger->info('Report generated and saved to MinIO', ['path' => $path]);

            // 4. Notify User via Mercure (Real-time)
            // Topic: /notifications/user/{userId} (Simplified to generic /notifications for MVP)
            $update = new Update(
                'http://oxford.edu/notifications',
                json_encode([
                    'status' => 'COMPLETED',
                    'message' => '¡Tu reporte está listo para descargar!',
                    'downloadUrl' => 'http://localhost:9000/oxford-storage/' . $path, // Direct MinIO link for demo
                    'timestamp' => time()
                ])
            );
            
            $this->hub->publish($update);
            $this->logger->info('Mercure notification sent');
            
        } catch (\Exception $e) {
            $this->logger->error('Report generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
