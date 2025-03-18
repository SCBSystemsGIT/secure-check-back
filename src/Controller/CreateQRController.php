<?php

namespace App\Controller;

use App\Entity\QRUser;
use App\Entity\User;
use App\Entity\Company;
use App\Helpers\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\FileUploader;


class CreateQRController extends AbstractController
{

    public function __construct(
        private Helpers $Helpers,
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private FileUploader $fileUploader,
    ) {
        // $this->Helpers = $Helpers;
    }   

    #[Route('/api/create-qr', name: 'app_create_q_r')]
    public function __invoke(Request $request): Response
{
    $data = $request->request->all();
        $file = $request->files->get('user_document');

    // Fetch the company entity from the database
    $company = $this->em->getRepository(Company::class)->find($data["company_id"]);

    if (!$company) {
        return $this->json(["message" => "Company not found"], 404);
    }

    // Check if the email already exists in the QRUser table
    $qr = $this->em->getRepository(QRUser::class)->findOneBy(['email' => $data['email']]);

    if ($qr) {
        $qr->setDateExp($data['dateExp']); 
        $uidn = $qr->getUidn();
        $qr->setUpdatedAt(new \DateTimeImmutable());
    } else {
        $qr = new QRUser();
        $qr->setEmail($data['email']);
        $qr->setFirstName($data['firstname']); 
        $qr->setLastName($data['lastname']);
        $qr->setContact($data['contact']);
        $qr->setTitle($data['title']);
        $qr->setDateExp($data['dateExp']);
        $qr->setType($data["type"]);
        $qr->setCompany($company);

        $uidn = uniqid();
        $qr->setUidn($uidn);
        $qr->setCode(
            $this->Helpers->generateEncryptQR($data['type'], $data, $uidn)
        );
        $qr->setCreatedAt(new \DateTimeImmutable());
        if ($file) {
            $uploadedFilePath = $this->fileUploader->upload($file, "user_document");
            $qr->setUserImage($uploadedFilePath);
        }
    }

    $this->em->persist($qr);
    $this->em->flush();

    return new JsonResponse([
        'status' => 'success',
        'message' => 'QR code updated successfully',
        'data' => ["uidn" => $qr->getUidn()]
    ], Response::HTTP_OK);
}

    // public function __invoke(Request $request): Response
    // {
    //     $data = json_decode($request->getContent(), true);
    
    //     // Check if the user exists in the User table
    //     // $user = $this->em
    //     //              ->getRepository(User::class)
    //     //              ->findOneBy(['email' => $data['email']]);
    
    //     // if ($user) {
    //     //     $userId = $user->getId(); 
    //     //     $companyId = $user->getCompany(); 
    //     // } else {
    //     //     return $this->json([
    //     //         "message" => "Le mail n'existe pas"
    //     //     ], 404);
    //     // }
    //     $company = $this->em->getRepository(Company::class)->find($data["company_id"]);

    //     if (!$company) {
    //         return $this->json(["message" => "Company not found"], 404);
    //     }
        
    //     // Set the company in QRUser
       
    //     // Check if the email already exists in the QRUser table
    //     $qr = $this->em
    //         ->getRepository(QRUser::class)
    //         ->findOneBy(['email' => $data['email']]);
    
    //     if ($qr) {
    //         $qr->setDateExp($data['dateExp']); 
    //         $uidn = $qr->getUidn();
    //         $qr->setUpdatedAt(new \DateTimeImmutable());
    //     } else {
    //         // Create new QRUser entry if not found
    //         $qr = new QRUser();
    //         $qr->setEmail($data['email']);
    //         $qr->setFirstName($data['firstname']); 
    //         $qr->setLastName($data['lastname']);
    //         $qr->setContact($data['contact']);
    //         $qr->setTitle($data['title']);
    //         $qr->setDateExp($data['dateExp']);
    //         $qr->setType($data["type"]);
    //         $qr->setCompanyId($company);
    
    //         $uidn = uniqid();
    //         $qr->setUidn($uidn);
    //         $qr->setCode(
    //             $this->Helpers->generateEncryptQR($data['type'], $data, $uidn)
    //         );
    //         $qr->setCreatedAt(new \DateTimeImmutable());
    
    //         // If the user has a company_id, set it in QRUser
    //         // if (!empty($companyId)) {
    //         //     $qr->setCompany($companyId);
    //         // }
    //     }
    
    //     // Persist and flush changes
    //     $this->em->persist($qr);
    //     $this->em->flush();
    
    //     // Generate encrypted QR code only for new entries
    //     if ($qr->getUidn()) {
    //         $this->Helpers->generateEncryptQR($data['type'], $data, $uidn);
    //     }
    
    //     return new JsonResponse([
    //         'status' => 'success',
    //         'message' => 'QR code updated successfully',
    //         'data' => [
    //             "uidn" => $qr->getUidn()
    //         ]
    //     ], Response::HTTP_OK);
    // }
    


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

    #[Route('/api/qruser/{id}', name: 'qruser', methods: ['GET', 'PUT'])]
    public function qrUser($id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(QrUser::class)->find($id);
    
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
    
        if ($request->isMethod('GET')) {
            return new JsonResponse([
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'contact' => $user->getContact(),
                'dateExp' => $user->getDateExp(),
            ]);
        }
    
        if ($request->isMethod('PUT')) {
            $data = json_decode($request->getContent(), true);
            if (isset($data['dateExp'])) {
                $user->setDateExp($data['dateExp']);
            }
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            return new JsonResponse(['message' => 'Expiration date updated successfully'], 200);
        }
    
        return new JsonResponse(['message' => 'Method Not Allowed'], 405);
    }
    
}
