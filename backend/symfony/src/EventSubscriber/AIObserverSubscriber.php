<?php

namespace App\EventSubscriber;

use App\Entity\Grade;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AIObserverSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->notifyAI('create', $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->notifyAI('update', $args);
    }

    private function notifyAI(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Only listen for Grade changes for now (Proactive Analysis)
        if (!$entity instanceof Grade) {
            return;
        }

        // Detect risk: Grade below 60
        if ($entity->getScore() < 60) {
            // Async notification to AI Service (Fire and Forget)
            // In a real prod env, use Messenger Queue. For MVP, we make a quick HTTP call or log context.
            // Using error_log for now to simulate "Sending to AI" without blocking
            error_log(sprintf(
                "🧠 [AI Proactivity] Low grade detected for Student %s in %s: %s. Action: %s",
                $entity->getStudent()->getId(),
                $entity->getSubject()->getName(),
                $entity->getScore(),
                $action
            ));

            // TODO: Call AI Service Endpoint
            // $this->httpClient->request('POST', 'http://localhost:8001/analytics/predict-risk', [
            //     'json' => ['student_id' => $entity->getStudent()->getId(), 'new_grade' => $entity->getScore()]
            // ]);
        }
    }
}
