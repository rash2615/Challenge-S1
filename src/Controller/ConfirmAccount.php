<?php

namespace App\Controller;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

#[AsController]
class ConfirmAccount
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(User $data, Request $request): Response|\Exception
    {
        $bodyContent = json_decode($request->getContent(), true);

        // ERROR: No token provided as body parameter
        if (empty($bodyContent) || !array_key_exists('token', $bodyContent)) {
            throw new BadRequestException("Le token est manquant dans les paramètres POST de la requête.");
        }

        // ERROR: Account already confirmed
        if (!is_null($data->getConfirmedAt())) {
            if (!is_null($data->getConfirmationToken())) {
                $data->setConfirmationToken(null);
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }

            throw new CustomUserMessageAccountStatusException("Votre compte est déjà confirmé.");
        }

        // ERROR: Token provided as body parameter do not match with the User's confirmation token
        if ($data->getConfirmationToken() !== $bodyContent['token']) {
            throw new BadRequestException("Token invalide.");
        }

        // SUCCESS
        $data->setConfirmationToken(null);
        $data->setConfirmedAt(new DateTime());

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return new JsonResponse(
            ['code' => Response::HTTP_OK, 'message' => "Votre compte a été confirmé avec succès."],
            Response::HTTP_OK
        );
    }
}
