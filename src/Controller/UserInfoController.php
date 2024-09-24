<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserInfoController extends AbstractController
{
    private $jwtTokenManager;
    private $userProvider;

    public function __construct(JWTTokenManagerInterface $jwtTokenManager, UserProviderInterface $userProvider)
    {
        $this->jwtTokenManager = $jwtTokenManager;
        $this->userProvider = $userProvider;
    }

    #[Route('/api/user/info', name: 'api_user_info', methods: ['GET'])]
    public function getUserInfo(Request $request): JsonResponse
    {
        // dd('OK');
        // Récupération du token depuis l'en-tête Authorization
        $authorizationHeader = $request->headers->get('Authorization');
        if (null === $authorizationHeader) {
            return new JsonResponse(['error' => 'Authorization header not found'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        
        // Décodez le token pour obtenir les informations de l'utilisateur
        try {
            $payload = $this->jwtTokenManager->decode($token);
            if (!$payload) {
                return new JsonResponse(['error' => 'Invalid token'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            // Récupération de l'utilisateur à partir de l'ID ou de l'email
            $user = $this->userProvider->loadUserByIdentifier($payload['username']); // ou $payload['id'] selon votre configuration

            if (!$user instanceof UserInterface) {
                return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            // Préparez les informations de l'utilisateur
            $userInfo = [
                'id' => $user->getId(),
                'username' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ];

            return new JsonResponse($userInfo);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }
}
