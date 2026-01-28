<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SettingRepository $settingRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $settings = $this->settingRepository->findAll();
        
        $data = [];
        foreach ($settings as $setting) {
            $val = $setting->getValue();
            $type = $setting->getType();
            
            if ($type === 'boolean') $val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
            elseif ($type === 'integer') $val = (int)$val;
            
            $data[$setting->getName()] = $val;
        }

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function update(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        foreach ($data as $key => $value) {
            $setting = $this->settingRepository->findOneBy(['name' => $key]);
            
            if (!$setting) {
                $setting = new Setting();
                $setting->setName($key);
            }

            // Determine type
            if (is_bool($value)) {
                $setting->setType('boolean');
                $value = $value ? '1' : '0';
            } elseif (is_int($value)) {
                $setting->setType('integer');
                $value = (string)$value;
            } else {
                $setting->setType('string');
            }

            $setting->setValue((string)$value);
            $this->em->persist($setting);
        }
        
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
