<?php

namespace App\Controller;

use App\Entity\QRUser;
use App\Entity\User;
use App\Helpers\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class CreateQRController extends AbstractController
{
    public function __construct(
        private Helpers $Helpers,
        private EntityManagerInterface $em,
        private MailerInterface $mailer
    ) {
    }

    #[Route('/api/create-qr', name: 'app_create_qr')]
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this
            ->em
            ->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);

        if (empty($user)) {
            return $this->json([
                "message" => "Le mail n'existe pas"
            ], 404);
        }

        $qr = new QRUser();
        $qr->setEmail($data['email']);
        $uidn = uniqid();
        $qr->setUidn($uidn);
        $qr->setType($data["type"]);

        if ($data["type"] == 'temp') {
            $qr->setDateExp($data['date_exp']);
        }

        $this->em->persist($qr);
        $this->em->flush();

        $this->Helpers->generateEncryptQR($data['type'], $data, $uidn);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'QR code créé avec succès',
            'data' => [
                "uidn" => $uidn
            ]
        ], Response::HTTP_OK);
    }
}
