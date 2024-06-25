<?php

namespace App\Controller;

use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reset-password', name: 'reset_password', methods: ['GET'])]
class ResetPassword
{
    public function __construct(
        private UserRepository $userRepository,
        private ResetPasswordRepository $resetPasswordRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {
    }

    public function __invoke(Request $request): Response|\Exception
    {
        $bodyContent = json_decode($request->getContent(), true);
        if (
            empty($bodyContent) || !array_key_exists('password', $bodyContent) ||
            !array_key_exists('token', $bodyContent)
        ) {
            throw new BadRequestException(
                "Le password ou le token ou les deux sont manquants dans les paramètres POST de la requête."
            );
        }

        $errors = $this->validator->validate($bodyContent['token'], [new Length(null, 10, 255), new Type('string')]);
        if (count($errors) > 0) {
            throw new BadRequestException("Token invalide.");
        }

        $resetPassword = $this->resetPasswordRepository->findOneBy(['token' => $bodyContent['token']]);
        if (is_null($resetPassword)) {
            throw new NotFoundHttpException("La ressource relative à cette fonctionnalité n'a pas été trouvée.");
        }

        if ($resetPassword->getExpiresAt() < new DateTimeImmutable()) {
            $this->entityManager->remove($resetPassword);
            $this->entityManager->flush();

            // throw new ExpiredTokenException("Token expiré.");
            dd("Token expiré.");
        }

        $user = $this->userRepository->find($resetPassword->getUser()->getId());
        $user->setPassword($this->passwordHasher->hashPassword($user, $bodyContent['password']));

        $this->entityManager->remove($resetPassword);
        $this->entityManager->flush();

        // return new JsonResponse(
        //     [
        //         'code' => Response::HTTP_OK,
        //         'message' => "Votre mot de passe a été changé avec succès.",
        //     ],
        //     Response::HTTP_OK
        // );
        dd("Votre mot de passe a été changé avec succès.");
    }
}
