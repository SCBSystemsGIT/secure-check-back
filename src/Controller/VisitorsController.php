<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Evenements;
use App\Entity\Visitors;
use App\Entity\User;
use App\Entity\QRCodes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\VisitorsRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use App\Helpers\Helpers;
use App\Services\FileUploader;
use App\Entity\Requests;

class VisitorsController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $serializer;
    private $validator;
    private $Helpers;
    private $visitorsRepository;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Helpers $Helpers,
        VisitorsRepository $visitorsRepository,
        UserRepository $userRepository,
        private FileUploader $fileUploader,
        private Helpers $helpers
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->Helpers = $Helpers;
        $this->visitorsRepository = $visitorsRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return Response
     **/
    #[Route('/visitors/list', name: 'app_visitors', methods: ['GET'])]
    public function visitorsList(EntityManagerInterface $entityManager): Response
    {
        $datas = $entityManager->getRepository(Visitors::class)->findAll(array("created_at" => "DESC"));
        return $this->json($datas, 200, [], [
            'groups' => 'visitor'
        ]);
    }

    /**  
     * Enregistrement d'un visiteur
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/visitor/create', name: 'create_visitor', methods: ['POST'])]
    public function createVisitor(Request $request): JsonResponse
{
    try {
        $data = $request->request->all();
        $file = $request->files->get('request_image');

        if ($data === null) {
            throw new \InvalidArgumentException('Invalid JSON data');
        }

        // Define required fields
        $requiredFields = ['firstname', 'lastname', 'email', 'contact', 'address'];

        // Validate required fields
        $missingFields = $this->Helpers->validateRequiredFields($data, $requiredFields);
        if (!empty($missingFields)) {
            throw new \InvalidArgumentException('Missing required fields: ' . implode(', ', $missingFields));
        }

        if (isset($data['user_id'])) {
            $user = $this->userRepository->find($data['user_id']);
            if (!$user) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }
        }

        $visitor = new Visitors();
        $eventExists = false;
        $companyExists = false;
        $uidn = null;

        if (!empty($data["evenements_id"])) {
            $event = $this->entityManager->getRepository(Evenements::class)->find($data['evenements_id']);
            if ($event) {
                $visitor->setEvenements($event);
                $eventExists = true;
            }
        }

        if (!empty($data["company_id"])) {
            $company = $this->entityManager->getRepository(Company::class)->find($data['company_id']);
            if ($company) {
                $visitor->setCompany($company);
                $companyExists = true;
            }
        }

        $visitor->setUser($user ?? null);
        $visitor->setFirstname($data['firstname']);
        $visitor->setLastname($data['lastname']);
        $visitor->setEmail($data['email']);
        $visitor->setContact($data['contact']);
        $visitor->setAddress($data['address']);
        $visitor->setOrganisationName($data['organisation_name']);
        $visitor->setVisitorType(!empty($data['visitor_type']) ? (int) $data['visitor_type'] : 2);
        $visitor->setIdNumber($data['id_number']);
        $visitor->setState($data['state']);
        $visitor->setCountry($data['country']);
        $visitor->setZipCode($data['zipcode']);
        $visitor->setCity($data['city']);
        $visitor->setRequestDate($data['request_date']);
        $visitor->setRequestTime($data['request_time']);
        $visitor->setCreatedAt(new \DateTimeImmutable());
        $visitor->setUpdatedAt(new \DateTimeImmutable());

        // Upload the file if present
        if ($file) {
            $uploadedFilePath = $this->fileUploader->upload($file, "request_image");
            $visitor->setRequestImage($uploadedFilePath);
        }

        // Persist visitor data
        $this->entityManager->persist($visitor);
        $this->entityManager->flush();

        // Retrieve visitor ID after saving
        $visitorId = $visitor->getId();

        // Generate QR code only if event and company exist
        if (!empty($data["company_id"]) && !empty($data["evenements_id"]) && $visitor->getEvenements() !== null) {
            $uidn = uniqid();
            $qrCodePath = $this->helpers->generateEncryptEvent($visitorId, $uidn);

            if ($qrCodePath) {
                $qrCode = new QRCodes();
                $qrCode->setVisitor($visitor);
                $qrCode->setCode($qrCodePath);
                $qrCode->setUidn($uidn);
                $qrCode->setRequestEvent($data["evenements_id"]);
                $qrCode->setType('Temporaire');
                $qrCode->setExpirationDate(new \DateTime('+1 day'));
                $qrCode->setStatus(1);
                $qrCode->setCreatedAt(new \DateTimeImmutable());
                $qrCode->setUpdatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($qrCode);
                $this->entityManager->flush();
            } else {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Failed to generate QR code'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        // Validate visitor entity before returning response
        $errors = $this->validator->validate($visitor);
        if (count($errors) > 0) {
            return $this->json([
                'status' => 'error',
                'message' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        // Custom message and status code based on event and company presence
        $message = (!empty($data["evenements_id"]) && !empty($data["company_id"]) && $visitor->getEvenements() !== null)
            ? 'Request for event created successfully'
            : 'Visitor created successfully';

        $statusCode = (!empty($data["evenements_id"]) && !empty($data["company_id"]) && $visitor->getEvenements() !== null) 
            ? Response::HTTP_OK
            : Response::HTTP_CREATED;

        return new JsonResponse([
            'status' => 'success',
            'message' => $message,
            'data' => [
                "contact" => $visitor->getContact(),
                "visitor_id" => $visitor->getId(),
                "uidn" => $uidn,
                "firstname" => $visitor->getFirstname(),
                "lastname" => $visitor->getLastname(),
                "address" => $visitor->getAddress(),
                "state" => $visitor->getState(),
                "country" => $visitor->getCountry(),
                "zipcode" => $visitor->getZipCode(),
                "city" => $visitor->getCity(),
            ]
        ], $statusCode);
    } catch (\InvalidArgumentException $e) {
        return $this->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], Response::HTTP_BAD_REQUEST);
    } catch (\Exception $e) {
        return $this->json([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    


}
