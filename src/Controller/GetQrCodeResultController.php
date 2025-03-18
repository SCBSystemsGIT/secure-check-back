<?php

namespace App\Controller;

use App\Entity\CheckIns;
use App\Entity\UserCheckIn;
use App\Repository\CheckInsRepository;
use App\Repository\QRCodesRepository;
use App\Repository\QRUserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserCheckInRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetQrCodeResultController extends AbstractController
{

    public function __construct(
        private QRCodesRepository $qRCodesRepo,
        private EntityManagerInterface $em,
        private CheckInsRepository $checkInsRepo,
        private UserCheckInRepository $userCheckInRepo,
        private QRUserRepository $qrUserCodesRepo,
        private Security $security
    ) {}
    #[Route('/api/get-qr-data/{uidn}',  methods: ['GET'])]
    public function getQrResult($uidn, Request $request)
    {
        $isManual = $request->query->get('type');
        $user = $this->security->getUser();
        
         // Get token from the cookie
        //  $token = $request->cookies->get('token');

        //  if (!$token) {
        //      return new JsonResponse(['message' => 'No token found in cookies'], 401);
        //  }

        $qr = $this->qRCodesRepo->findOneBy(['uidn' => $uidn]);

        if (!empty($qr)) {
            if ($qr->isUsed()) {
                return $this->json(
                    [
                        'status' => 'success',
                        'message' => "Déja utilisé",
                    ],
                    Response::HTTP_OK
                );
            }
        } else {
            return $this->json(
                [
                    'status' => 'success',
                    'message' => "Non Valide"
                ],
                Response::HTTP_OK
            );
        }


        $currentDateTime = new DateTime();
        if ($currentDateTime > $qr->getExpirationDate()) {
            return $this->json(
                [
                    'status' => 'error',
                    'message' => "Le QR code a expiré"
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $isAlreadyCheckIn = $this->checkInsRepo->findBy(['qr_code' => $qr]);
        if (sizeof($isAlreadyCheckIn) > 0) {
            $this->updateCheckIn($qr);

            if ($isManual) {
                return $this->json([
                    'status' => 'success',
                    'message' => "CheckOut éffectué"
                ], Response::HTTP_OK);
            }
            $url = $this->getParameter('domain_front') . '/success-checkout/' . $uidn;
                return $this->redirect($url);
        }

        $this->saveCheckIn($qr);

        if ($isManual) {
            return $this->json([
                'status' => 'success',
                'message' => "CheckIn éffectué"
            ], Response::HTTP_OK);
        }
        $url = $this->getParameter('domain_front') . '/success-checkin/' . $uidn;
        return $this->redirect($url);
    }


    public function saveCheckIn($qr)
    {
        $created_date = $qr->getCreatedAt();
        
        //echo $created_date; die;
        $checkIn = new CheckIns();
        $checkIn->setCheckInTime(new DateTimeImmutable());
        $checkIn->setVisitor($qr->getVisitor());
        $checkIn->setQrCode($qr);
        $checkIn->setCreatedAt($created_date);

        $qr->addCheckIn($checkIn);
        $this->em->persist($checkIn);
        $this->em->persist($qr);
        $this->em->flush();
    }

    /*public function updateCheckIn($qr)
    {
        $checkIn = $this->checkInsRepo->findOneBy(['qr_code' => $qr]);
        $checkIn->setCheckOutTime(new DateTimeImmutable());
        $qr->addCheckIn($checkIn);
        $this->em->persist($checkIn);

        $qr->setUsed(true);
        $this->em->persist($qr);
        $this->em->flush();
    }*/
    public function updateCheckIn($qr)
    {
        $checkIn = $this->checkInsRepo->findOneBy(['qr_code' => $qr]);

        if (!$checkIn) {
            throw new \Exception('Check-in not found for the provided QR code.');
        }

        if ($qr->getVisitor()->getVisitorType() === 1) {
            if ($checkIn->getCheckOutTime() !== null) {
                $checkIn->setCheckInTime(new DateTimeImmutable());  
                $checkIn->setCheckOutTime(null); 
            } else {
                $checkIn->setCheckOutTime(new DateTimeImmutable());
            }
        } else {
            if ($checkIn->getCheckOutTime() === null) {
                $checkIn->setCheckOutTime(new DateTimeImmutable());
            } else {
                throw new \Exception('Temporary QR code has already been checked out.');
            }

            $qr->setUsed(true);  
            $this->em->persist($qr);
        }

        $this->em->persist($checkIn); 
        $this->em->flush(); 
    }

    // User login Qr
    #[Route('/api/get-qr-user-data/{uidn}',  methods: ['GET'])]
    public function getUserQrResult($uidn, Request $request)
    {

        $isManual = $request->query->get('type');

        $qr = $this->qrUserCodesRepo->findOneBy(['uidn' => $uidn]);
     
        if (!empty($qr)) {
            if ($qr->isUsed()) {
                return $this->json(
                    [
                        'status' => 'success',
                        'message' => "Déja utilisé"
                    ],
                    Response::HTTP_OK
                );
            }
        } else {
            return $this->json(
                [
                    'status' => 'success',
                    'message' => "Non Valide"
                ],
                Response::HTTP_OK
            );
        }

        $currentDateTime = new DateTime();
            $dateExp = $qr->getDateExp() ? new DateTime($qr->getDateExp()) : null;

            if ($dateExp && $currentDateTime > $dateExp) {
                return $this->json(
                    [
                        'status' => 'error',
                        'message' => "Le QR code a expiré"
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }


        $isAlreadyCheckIn = $this->userCheckInRepo->findBy(['qr_user' => $qr]);
        if (sizeof($isAlreadyCheckIn) > 0) {
            $this->updateUserCheckIn($qr);

            if ($isManual) {

                return $this->json(
                    [
                        'status' => 'success',
                        'message' => "CheckOut éffectué"
                    ],
                    Response::HTTP_OK
                );
            }

            $url = $this->getParameter('domain_front') . '/success-user-checkout/' . $uidn;
            return $this->redirect($url);
        }
       
         $this->saveUserCheckIn($qr);

        if ($isManual) {

            return $this->json(
                [
                    'status' => 'success',
                    'message' => "CheckIn éffectué"
                ],
                Response::HTTP_OK
            );
        }
        $url = $this->getParameter('domain_front') . '/success-user-checkin/' . $uidn;

        return $this->redirect($url);
    }

    
    public function saveUserCheckIn($qr)
    {
        $created_date = $qr->getCreatedAt();
        $userCheckIn = new UserCheckIn();
        $userCheckIn->setCheckInTime(new DateTimeImmutable());
        $userCheckIn->setQrUser($qr);
        $userCheckIn->setCreatedAt($created_date);
        $qr->addUserCheckIn($userCheckIn);
        $this->em->persist($userCheckIn);
        $this->em->persist($qr);
        $this->em->flush();
    }

    public function updateUserCheckIn($qr)
    {
        $userCheckIn = $this->userCheckInRepo->findOneBy(['qr_user' => $qr]);
        if (!$userCheckIn) {
            throw new \Exception('Check-in not found for the provided QR code.');
        }

        if ($qr->getType() == 'permanent') {
            if ($userCheckIn->getCheckOutTime() !== null) {
                $userCheckIn->setCheckInTime(new DateTimeImmutable());  
                $userCheckIn->setCheckOutTime(null); 
            } else {
                $userCheckIn->setCheckOutTime(new DateTimeImmutable());
            }
        } else {
            if ($userCheckIn->getCheckOutTime() === null) {
                $userCheckIn->setCheckOutTime(new DateTimeImmutable());
            } else {
                throw new \Exception('Temporary QR code has already been checked out.');
            }

            $qr->setUsed(true);  
            $this->em->persist($qr);
        }

        $this->em->persist($userCheckIn); 
        $this->em->flush(); 
    }
    
}
