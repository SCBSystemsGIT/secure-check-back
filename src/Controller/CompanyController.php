<?php

namespace App\Controller;
use App\Entity\QRUser;
use App\Entity\User;
use App\Helpers\Helpers;
use App\Entity\Company;
use App\Services\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    private $companyService;
    private $helpers;

    public function __construct(CompanyService $companyService, Helpers $helpers)
    {
        $this->companyService = $companyService;
        $this->helpers = $helpers; // Initialize the Helpers property
    }

    #[Route('/api/company', name: 'company_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dataone = $request->request->all();
        $name = $dataone["name"];
        $address = $dataone["address"];
        $id_number = $dataone["id_number"];
        $point_contact = $dataone["point_contact"];
        $email = $dataone["email"];
        $phone_number = $dataone["phone_number"];
        $company_field = $dataone["company_field"];
        $country = $dataone["country"];
        $city = $dataone["city"];
        $state = $dataone["state"];
        $title = $dataone["title"];
        $number_of_employee = $dataone["number_of_employee"];
        $slugs = strtolower($name);
        $slug = str_replace(' ', '-', $slugs);
        //$slug = "https://www.securecheck.info/$slug1";
        $this->helpers->generateCompanyQR($slug, $data);
        if (empty($data)) {
            $data = $request->request->all();
        }
        $file = $request->files->get('logo');
        return $this->companyService->add($data, $file);
    }

    #[Route('/api/company/{slug}', name: 'company_show', methods: ['GET'])]
    public function show($slug): JsonResponse
    {
        return $this->companyService->show($slug);
    }

    #[Route('/api/company/{id}', name: 'company_update', methods: ['POST'])]
    public function update(Request $request, Company $company): JsonResponse
    {
         $data = $request->request->all();
         $file = $request->files->get('logo');
         $name = $data["name"];
         $slugs = strtolower($name);
         $slug = str_replace(' ', '-', $slugs);
         $this->helpers->generateCompanyQR($slug, $company);
        return $this->companyService->update($data, $company ,$file);
    }

    #[Route('/api/company/{id}', name: 'company_delete', methods: ['DELETE'])]
    public function delete(Company $company): JsonResponse
    {
        return $this->companyService->delete($company);
    }

    #[Route('/api/companies', name: 'company_all', methods: ['GET'])]
    public function all(): JsonResponse
    {
        return $this->companyService->all();
    }
}
