<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {

            return $this->redirectToRoute('app_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/signin.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot_password', name:'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $UserRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $UserRepository->findOneByEmail($form->get('email')->getData());

            if($user){
                // $token = $tokenGenerator->generateToken();
                // $user->setResetToken($token);

                // users_reset_password

                // Colonne	Type	Commentaire
                // id	integer Incrément automatique [nextval('users_reset_password_id_seq')]	
                // user_id	integer	
                // token	character varying(255)	
                // requested_at	timestamp(0)	
                // expires_at	timestamp(0)

                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user);
                $entityManager->persist($resetPassword);

                // $resetPassword->setToken($token);
                // $resetPassword->setRequestedAt(new \DateTimeImmutable());
                // $resetPassword->setExpiresAt(new \DateTimeImmutable('+1 hour'));

                $entityManager->persist($user);
                $entityManager->flush();

                $url = $this->generateUrl('reset_pass', ['token' => $resetPassword->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $context = compact('url', 'user');

                $mail->send(
                    'no-reply@luxar.space',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'emails/password_reset.html.twig',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/forgot_password/{token}', name:'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $resetPassword = $entityManager->getRepository(ResetPassword::class)->findOneByToken($token);
        
        if($resetPassword->getUser())
        {
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                $user = $resetPassword->getUser();

                $entityManager->remove($resetPassword);

                $user->setPassword($form->get('password')->getData());
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}
