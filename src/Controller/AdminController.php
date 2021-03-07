<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Security\AppUserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/edit_city', name: 'edit_city')]
    public function editCity(CityRepository $cityRepository, Request $request, SerializerInterface $serializer): Response
    {
        $cityList = $cityRepository ->findAll();

        if ($request->isXmlHttpRequest()) {

            $cityData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $cityName = $propertyAccessor->getValue($cityData, '[city]');
            $cityCode = $propertyAccessor->getValue($cityData, '[code]');

            $city = new City();
            $city ->setName($cityName);
            $city -> setCode($cityCode);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($city);
            $entityManager->flush();

            $cityList = $cityRepository ->findAll();
            dump($cityList);

            $cityListJson = $serializer->serialize($cityList, 'json', ['groups' => ['city']]);
            dump($cityListJson);

            return new Response($cityListJson);
/*            return $this->render('admin/edit_city.html.twig', [
            'cityList'=>$cityList,
            ]);*/
        }

        return $this->render('admin/edit_city.html.twig', [
            'cityList'=>$cityList,
        ]);
    }

    #[Route('/admin/edit_campus', name: 'edit_campus')]
    public function editCampus(CampusRepository $campusRepository): Response
    {
        $campusList = $campusRepository ->findAll();

        return $this->render('admin/edit_campus.html.twig', [
            'campusList'=>$campusList,
        ]);
    }

    #[Route('/admin/user', name: 'user_admin')]
    public function adminUser(CampusRepository $campusRepository): Response
    {
        $campusList = $campusRepository ->findAll();

        return $this->render('admin/user_admin.html.twig', [
            'campusList'=>$campusList,
        ]);
    }

    #[Route('/admin/user_manage', name: 'user_manage')]
    public function userManage(UserRepository $userRepository): Response
    {
        $userList = $userRepository ->findAll();

        return $this->render('admin/user_manage.html.twig', [
            'userList'=>$userList,
        ]);
    }

    #[Route('/admin/user_register', name: 'app_register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppUserAuthenticator $authenticator): Response
    {
        $user = new User();
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
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('admin/user_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


}
