<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Company;
use App\Entity\CompanyType;
use App\Repository\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Helpers\Helpers;

class CompanyListController extends AbstractController
{
	private $entityManager;
    private $validator;
    private $resourceName = 'Entreprise';

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
	
    #[Route('/api/companylist', name: 'app_company_list')]
    public function getcompanylist(EntityManagerInterface $entityManager): JsonResponse
    {
		//$companies = $this->$entityManager->getRepository(Company::class)->findBy([], ['createdAt' => 'DESC']);
		$data = $this->entityManager->getRepository(Company::class)->findBy([], ['createdAt' => 'DESC']);
      return $this->json(
            [
                'data' => $data
            ],
            Response::HTTP_OK,
            [],
            ["groups" => 'company']
        );
	

    return new JsonResponse($data);
    }
}
