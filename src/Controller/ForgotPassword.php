<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
// use App\Notification\UserNotification;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/forgot-password', name: 'forgot_password', methods: ['GET'])]
class ForgotPassword extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        // private UserNotification $userNotification
    ) {
    }

    public function __invoke(User $data): Response|\Exception
    {
        $email = $data->getEmail();
        if (is_null($email)) {
            throw new BadRequestException("L'adresse email doit être passé comme paramètre dans le requête POST.");
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (is_null($user)) {
            throw new NotFoundHttpException("L'utilisateur n'a pas été trouvé.");
        }

        $resetPassword = (new ResetPassword())->setUser($user);
        $this->entityManager->persist($resetPassword);
        $this->entityManager->flush();

        // $this->userNotification->sendResetPasswordMail($user, $resetPassword);

        dd($resetPassword);
        // return new JsonResponse(
        //     [
        //         'code' => Response::HTTP_OK,
        //         'message' => "Le mail a été envoyé avec succès.",
        //     ],
        //     Response::HTTP_OK
        // );
    }
}
