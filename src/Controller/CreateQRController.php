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
use Symfony\Component\Routing\Attribute\Route;

class CreateQRController extends AbstractController
{

    public function __construct(
        private Helpers $Helpers,
        private EntityManagerInterface $em
    ) {
        // $this->Helpers = $Helpers;
    }

    #[Route('/api/create-qr', name: 'app_create_q_r')]
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $qr = new QRUser();

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (empty($user)) {
            return $this->json([
                "message" => "le mail n'existe pas"
            ], 404);
        }

        $qr->setEmail($data['email']);
        $uidn = uniqid();
        $qr->setUidn(uidn: $uidn);
        $qr->setType($data["type"]);

        $this->em->persist($qr);
        $this->em->flush();

        $this->Helpers->generateEncryptQR($data['type'], $data, $uidn);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Request and QR code updated successfully',
            'data' => [
                "qr" => $qr
            ]
        ], Response::HTTP_OK);
    }
}
