<?php

namespace App\Controller;

use App\Entity\CheckIns;
use App\Repository\QRCodesRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetQrCodeResultController extends AbstractController
{

    public function __construct(private QRCodesRepository $qRCodesRepo, private EntityManagerInterface $em) {}

    #[Route('/get-qr-data/{uidn}')]
    public function getQrResult($uidn)
    {

        $qr = $this->qRCodesRepo->findOneBy(['uidn' => $uidn]);
        $currentDateTime = new DateTime();

        if ($currentDateTime > $qr->getExpirationDate()) {
            return $this->json(
                [
                    'status' => 'error',
                    'message' => "Le QR code est expiré"
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $checkIn = new CheckIns();
        $checkIn->setCheckInTime(new DateTimeImmutable());
        $checkIn->setVisitor($qr->getVisitor());
        $checkIn->setQrCode($qr);
        $checkIn->setCreatedAt(new DateTimeImmutable());

        $qr->addCheckIn($checkIn);
        $this->em->persist($checkIn);

        $qr->setUsed(true);
        $this->em->persist($qr);
        $this->em->flush();

        return $this->json(
            [
                'status' => 'success',
                'message' => "CheckIn éffectué"
            ],
            Response::HTTP_OK
        );
    }
}
