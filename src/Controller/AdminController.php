<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use League\Csv\Reader;

class AdminController extends AbstractController
{
    #[Route('/admin/edit_city', name: 'edit_city')]
    public function editCity(CityRepository $cityRepository, Request $request, SerializerInterface $serializer): Response
    {
        $cityList = $cityRepository->findAll();

        if ($request->isXmlHttpRequest()) {

            $cityData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $cityName = $propertyAccessor->getValue($cityData, '[city]');
            $cityCode = $propertyAccessor->getValue($cityData, '[code]');

            $city = new City();
            $city->setName($cityName);
            $city->setCode($cityCode);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($city);
            $entityManager->flush();

            /*            $cityList = $cityRepository->findAll();
                        $cityListJson = $serializer->serialize($cityList, 'json', ['groups' => ['city']]);  */

            return new Response("Hello! City added");
        }

        return $this->render('admin/edit_city.html.twig', [
            'cityList' => $cityList,
        ]);
    }

    #[Route('/admin/mod_city', name: 'mod_city')]
    public function removeCity(Request $request, CityRepository $cityRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $cityData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $cityCode = $propertyAccessor->getValue($cityData, '[code]');
            $cityX = $cityRepository->findOneBy(['code' => $cityCode]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cityX);
            $entityManager->flush();

            return new Response('Done! City removed successfully!');
        }
    }

    #[Route('/admin/edit_campus', name: 'edit_campus')]
    public function editCampus(CampusRepository $campusRepository, Request $request, SerializerInterface $serializer): Response
    {
        $campusList = $campusRepository->findAll();

        if ($request->isXmlHttpRequest()) {

            $campusData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $campusName = $propertyAccessor->getValue($campusData, '[campus]');

            $campus = new Campus();
            $campus->setName($campusName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();

            return new Response("Hello! New campus added!");
        }

        return $this->render('admin/edit_campus.html.twig', [
            'campusList' => $campusList,
        ]);
    }

    #[Route('/admin/mod_campus', name: 'mod_campus')]
    public function removeCampus(Request $request, CampusRepository $campusRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $campusData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $campusName = $propertyAccessor->getValue($campusData, '[campus]');
            $campusX = $campusRepository->findOneBy(['name' => $campusName]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campusX);
            $entityManager->flush();

            return new Response('Done! Campus removed successfully!');
        }
    }

    #[Route('/admin/user_suspend', name: 'user_suspend')]
    public function userSuspend(Request $request, UserRepository $userRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $userData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $userName = $propertyAccessor->getValue($userData, '[user]');
            $user = $userRepository->findOneBy(['name' => $userName]);
            $userStatus = $user->getIsActive();

            if ($userStatus == "") {
                $user->setIsActive(true);
            } else {
                $user->setIsActive(false);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $userList = $userRepository->findAll();
            return $this->render('templates/_user_table.html.twig', [
                'userList' => $userList,
            ]);
        }
    }

    #[Route('/admin/user_delete', name: 'user_delete')]
    public function userDelete(Request $request, UserRepository $userRepository): Response
    {
        if ($request->isXmlHttpRequest()) {

            $userData = $request->request->all();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $userName = $propertyAccessor->getValue($userData, '[user]');
            $user = $userRepository->findOneBy(['name' => $userName]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            return new Response('Hello');
        }
    }

    #[Route('/admin/user', name: 'user_admin')]
    public function adminUser(): Response
    {
        return $this->render('admin/user_admin.html.twig');
    }

    #[Route('/admin/user_manage', name: 'user_manage')]
    public function userManage(UserRepository $userRepository): Response
    {
        $userList = $userRepository->findAll();

        return $this->render('admin/user_manage.html.twig', [
            'userList' => $userList,
        ]);
    }

    #[Route('/admin/user_register', name: 'app_register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
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

            $this->addFlash(
                'notice',
                'New user registered',
            );

            return $this->render('admin/user_admin.html.twig');
        }

        return $this->render('admin/user_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/user_csv', name: 'user_csv')]
    public function userCSV(): Response
    {
        return $this->render('admin/user_csv.html.twig');
    }

    #[Route('/admin/user_csv_upload', name: 'user_csv_upload')]
    public function userCSVUpload(UserPasswordEncoderInterface $passwordEncoder, CampusRepository $campusRepository): Response
    {
        $csv = Reader::createFromPath('C:\Users\Kat\Downloads\user.csv', 'r');
        $csv->setHeaderOffset(0);
        $header = $csv->getHeader(); //returns ['First Name', 'Last Name', 'E-mail', etc]

        $records = $csv->getRecords();
        foreach ($records as $offset => $record) {
            $user = new User();
            $campus = $campusRepository->findOneBy(['name' => $record ['campus']]);
            $user->setUsername($record ['username']);
            $user->setName($record ['name']);
            $user->setLastName($record ['last_name']);
            $user->setEmail($record ['email']);
            $user->setPhone($record ['phone']);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $record ['password']
                )
            );
            $user->setCampus($campus);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }
        $this->addFlash(
            'notice',
            'User group registered',
        );

        return $this->render('admin/user_admin.html.twig');
    }
}
