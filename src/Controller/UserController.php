<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/confirm_account', name: 'app_user_confirm_account', methods: ['GET'])]
    public function confirmAccount(User $user, Request $request): Response|\Exception
    {
        dd($user);
        // $bodyContent = json_decode($request->getContent(), true);

        // // ERROR: No token provided as body parameter
        // if (empty($bodyContent) || !array_key_exists('token', $bodyContent)) {
        //     throw new BadRequestException("Le token est manquant dans les paramètres POST de la requête.");
        // }

        // // ERROR: Account already confirmed
        // if (!is_null($data->getConfirmedAt())) {
        //     if (!is_null($data->getConfirmationToken())) {
        //         $data->setConfirmationToken(null);
        //         $this->entityManager->persist($data);
        //         $this->entityManager->flush();
        //     }

        //     throw new CustomUserMessageAccountStatusException("Votre compte est déjà confirmé.");
        // }

        // // ERROR: Token provided as body parameter do not match with the User's confirmation token
        // if ($data->getConfirmationToken() !== $bodyContent['token']) {
        //     throw new BadRequestException("Token invalide.");
        // }

        // // SUCCESS
        // $data->setConfirmationToken(null);
        // $data->setConfirmedAt(new DateTime());

        // $this->entityManager->persist($data);
        // $this->entityManager->flush();

        // return new JsonResponse(
        //     ['code' => Response::HTTP_OK, 'message' => "Votre compte a été confirmé avec succès."],
        //     Response::HTTP_OK
        // );
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_customers_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/customers/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
