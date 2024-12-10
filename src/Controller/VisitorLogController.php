<?php

namespace App\Controller;

use App\Entity\CheckIns;
use App\Repository\CheckInsRepository;
use App\Repository\QRCodesRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Helpers\Helpers;

class VisitorLogController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator,
        Helpers $Helpers,
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->Helpers = $Helpers;
    }

    #[Route('/api/visitorlog', name: 'api_visitorlog', methods: ['GET'])]
public function getVisitorLog(EntityManagerInterface $entityManager): JsonResponse
{
    // Retrieve all CheckIns entities sorted by created_at in descending order
    $datas = $entityManager->getRepository(CheckIns::class)->findBy([], ['created_at' => 'DESC']);

    // Format the data
    $data = [];
    foreach ($datas as $checkIn) {
        $data[] = [
            'id' => $checkIn->getId(),
            'visitor_id' => $checkIn->getVisitor()->getId(),
            //->getVisitor()->getId(),
            //'qr_code_id' => $checkIn->getQrCodeId(),
            'check_in_time' => $checkIn->getCheckInTime() ? $checkIn->getCheckInTime()->format('Y-m-d H:i:s') : null,
            'check_out_time' => $checkIn->getCheckOutTime() ? $checkIn->getCheckOutTime()->format('Y-m-d H:i:s') : null,
            'created_at' => $checkIn->getCreatedAt() ? $checkIn->getCreatedAt()->format('Y-m-d H:i:s') : null,
            'updated_at' => $checkIn->getUpdatedAt() ? $checkIn->getUpdatedAt()->format('Y-m-d H:i:s') : null,
        ];
    }

    return new JsonResponse($data);

}

}
