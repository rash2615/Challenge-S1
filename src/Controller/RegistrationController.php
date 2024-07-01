<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    // private EmailVerifier $emailVerifier;

    // public function __construct(EmailVerifier $emailVerifier)
    // {
    //     $this->emailVerifier = $emailVerifier;
    // }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // dd($form, $request);
        if ($form->isSubmitted() && $form->isValid()) {
            $confirmpassword= $request->getPayload()->get('confirmpassword');
            // dd($confirmpassword);
            //plainpassword = confirmplainpassword
            if (
                $form->get('password')->getData() !== $confirmpassword
            ) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('app_register');
            }
            // encode the plain password
            $user->setPassword(
                    $form->get('password')->getData()
            );
            // dd($user);
            // Set additional fields that are not included in the form but required in the database
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            // Commenting out email verification logic
            // generate a signed url and email it to the user
            // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //     (new TemplatedEmail())
            //         ->from(new Address('contact@luxar.com', 'luxar'))
            //         ->to($user->getEmail())
            //         ->subject('Please Confirm your Email')
            //         ->htmlTemplate('emails/confirmation_email.html.twig')
            // );

            // do anything else you need here, like send an email

            // Redirect to login page or another appropriate page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
