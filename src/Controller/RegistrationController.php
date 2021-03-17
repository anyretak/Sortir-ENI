<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class RegistrationController extends AbstractController
{
    #[Route('/modify_user', name: 'modify_user')]
    public function modify(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Success! Your profile was updated.'
            );
        }

        //refresh user (but better to make DTO)
        $this->getDoctrine()->getManager()->refresh($user);

        return $this->render('registration/modify_user.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/{user}', name: 'user_profile')]
    public function userProfile($user, UserRepository $userRepository, UploaderHelper $helper): Response
    {
        $userDetails = $userRepository->findBy(['name' => $user]);
        return $this->render('registration/user_profile.html.twig', [
            'userDetails' => $userDetails,
        ]);
    }
}
