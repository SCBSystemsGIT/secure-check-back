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
<<<<<<< HEAD
    }   

    #[Route('/api/create-qr', name: 'app_create_qr')]
=======
        // $this->Helpers = $Helpers;
    }   

    #[Route('/api/create-qr', name: 'app_create_q_r')]
>>>>>>> bd12b5f7d17be2589322043848985aee0b166bc6
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $qr = new QRUser();

<<<<<<< HEAD
        $admin = $this->getUser();
        if(empty($admin)){
            return $this->json([
                "message" => "N'est pas autorisé a éffectué cette action"
            ], 404);
        }

        $user = $this
                    ->em
                    ->getRepository(User::class)
                    ->findOneBy(['email' => $data['email']]);

        
=======
        $user = $this
                        ->em
                        ->getRepository(User::class)
                        ->findOneBy(['email' => $data['email']]);

>>>>>>> bd12b5f7d17be2589322043848985aee0b166bc6
        if (empty($user)) {
            return $this->json([
                "message" => "le mail n'existe pas"
            ], 404);
        }

<<<<<<< HEAD
        if(!$admin->getCompany()->getSlug() == "scb"){
            
            if($user->getCompany()->getSlug() != $admin->getCompany()->getSlug()){
                return  $this->json([
                    "message"=>"L'utilisateur doit etre de votre entreprise"
                ]);
            }
            
        }

=======
>>>>>>> bd12b5f7d17be2589322043848985aee0b166bc6
        $qr->setEmail($data['email']);  
        $uidn = uniqid();
        $qr->setUidn(uidn: $uidn);
        $qr->setType($data["type"]);

        if($data["type"] == 'temp'){
<<<<<<< HEAD
            $qr->setDateExp($data['date_exp']);
=======
            $qr->setType($data['date_exp']);
>>>>>>> bd12b5f7d17be2589322043848985aee0b166bc6
        }

        $this->em->persist($qr);
        $this->em->flush();

        $this->Helpers->generateEncryptQR($data['type'], $data, $uidn);
        // TODO sendEmail
        // $this->sendEmail($this->mailer, $data['email']);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Request and QR code updated successfully',
            'data' => [
                "uidn" => $uidn
            ]
        ], Response::HTTP_OK);
    }

<<<<<<< HEAD
=======

>>>>>>> bd12b5f7d17be2589322043848985aee0b166bc6
    private function sendEmail(MailerInterface $mailer, $to): Response
    {
        // Créez l'email
        $email = (new Email())
            // ->from('your_email@example.com')
            ->from('noreply@express54.org')
            ->to($to)
            ->subject('Secure Check - QRCode')
            ->text('Votre QRcode')
            ->html('<p> Cher client votre QR est en PJ</p>');

        try {
            $mailer->send($email);
            return new Response('Email sent successfully');
        } catch (\Exception $e) {
            return new Response('Failed to send email: ' . $e->getMessage());
        }
    }
}