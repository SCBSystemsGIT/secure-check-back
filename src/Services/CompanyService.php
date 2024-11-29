<?php

namespace App\Services;

use App\Entity\Company;
use App\Entity\CompanyType;
use App\Repository\CompanyRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class CompanyService
{
    private string $resourceName = 'Entreprise';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private SluggerInterface $slugger,
        private CompanyRepository $companyRepository,
        private FileUploader $fileUploader
    ) {}

    public function add($data, $file)
    {
        $company = new Company();
        $company->setName($data['name'])
            ->setDescription($data['description'])
            ->generateSlug($this->slugger)
            ->setCreatedAt(new DateTimeImmutable());

        $errors = $this->validator->validate($company);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!empty($file)) {
            $logoPath = $this->fileUploader->upload($file, 'logo');
            $company->setLogo($logoPath);
        }

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'message' => 'Création effectuée',
                'data' => $company
            ],
            Response::HTTP_OK
        );
    }

    public function show($slug)
    {
        $company = is_numeric($slug)
            ? $this->companyRepository->find($slug)
            : $this->companyRepository->findOneBy(['slug' => $slug]);

        if (!$company) {
            return new JsonResponse(
                ['message' => "$this->resourceName introuvable"],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            [
                'message' => 'Entreprise',
                'data' => $company
            ],
            Response::HTTP_OK,
            [],
            ['groups' => 'company']
        );
    }

    public function update($data, $company, $file = null)
    {
        if (!$company) {
            return new JsonResponse(
                ['message' => "$this->resourceName introuvable"],
                Response::HTTP_NOT_FOUND
            );
        }

        $company->setName($data['name'])
            ->setDescription($data['description'])
            ->generateSlug($this->slugger);

        if (!empty($file)) {
            $logoPath = $this->fileUploader->upload($file, 'logo');
            $company->setLogo($logoPath);
        }

        $errors = $this->validator->validate($company);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        return new JsonResponse(
            [
                'message' => 'Mise à jour effectuée',
                'data' => $company
            ],
            Response::HTTP_OK,
            [],
            ['groups' => 'company']
        );
    }

    public function delete($company)
    {
        if (!$company) {
            return new JsonResponse(
                ['message' => "$this->resourceName introuvable"],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->entityManager->remove($company);
        $this->entityManager->flush();

        return new JsonResponse(
            ['message' => 'Suppression effectuée'],
            Response::HTTP_NO_CONTENT
        );
    }

    public function all()
    {
        $companies = $this->companyRepository->findBy([], ['createdAt' => 'DESC']);

        return new JsonResponse(
            ['data' => $companies],
            Response::HTTP_OK,
            [],
            ['groups' => 'company']
        );
    }
}
