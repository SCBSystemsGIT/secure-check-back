<?php

namespace App\Controller;
use App\Entity\Company;
use App\Entity\Evenements;
use App\Entity\Visitors;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\VisitorsRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Helpers\Helpers;
use App\Services\FileUploader;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventAttendanceController extends AbstractController
{
	private $entityManager;
    private $passwordHasher;
    private $serializer;
    private $validator;
    private $Helpers;
    private $visitorsRepository;
    private $userRepository;
	private $evenements;

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
	
    #[Route('/api/eventattendencelist', name: 'app_event_attendance')]
    public function geteventattendencelist(EntityManagerInterface $entityManager): JsonResponse
    {
		/*$data = [
    [
        'id' => 1,
        'name' => 'Tech Corp',
        'description' => 'A leading technology company specializing in AI solutions.',
        'slug' => 'tech-corp',
        'logo' => 'https://example.com/logos/tech-corp.png',
    ],
    [
        'id' => 2,
        'name' => 'Green Innovators',
        'description' => 'Pioneering eco-friendly and sustainable products.',
        'slug' => 'green-innovators',
        'logo' => 'https://example.com/logos/green-innovators.png',
    ],
    [
        'id' => 3,
        'name' => 'Finance Hub',
        'description' => 'Your trusted partner for financial planning and investment.',
        'slug' => 'finance-hub',
        'logo' => 'https://example.com/logos/finance-hub.png',
    ],
    [
        'id' => 4,
        'name' => 'EduLearn',
        'description' => 'Online education platform offering diverse courses.',
        'slug' => 'edulearn',
        'logo' => 'https://example.com/logos/edulearn.png',
    ],
    [
        'id' => 5,
        'name' => 'Foodies Delight',
        'description' => 'Connecting food lovers with the best culinary experiences.',
        'slug' => 'foodies-delight',
        'logo' => 'https://example.com/logos/foodies-delight.png',
    ],
];*/
$datas = $entityManager->getRepository(Evenements::class)->findBy([], ['date_event' => 'DESC', 'time_event' => 'DESC']);
foreach ($datas as $event) {
   $event_id = (string) $event->getId();
    $visitors = $entityManager->getRepository(Visitors::class)->findBy(['evenements' => $event_id]);
    if ($visitors) {
        $responseData[] = [
            'event' => $event,
            'visitors' => $visitors,
        ];
    }
    
}
// Return the response as a JSON
return $this->json($responseData, 200, [], [
    'groups' => ['visitor', 'evenements'], // Include both groups
]);      
    }
}
