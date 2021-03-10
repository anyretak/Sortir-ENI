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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use League\Csv\Reader;

class AdminController extends AbstractController
{
    //******************************************************************//
    //**************************MANAGE CITY*****************************//
    //******************************************************************//
    #[Route('/admin/admin_city', name: 'admin_city')]
    public function editCity(CityRepository $cityRepository): Response
    {
        $cityList = $cityRepository->findAll();
        return $this->render('admin/admin_city.html.twig', [
            'cityList' => $cityList,
        ]);
    }

    #[Route('/admin/add_city', name: 'add_city')]
    public function addCity(Request $request): Response
    {
        $data = $request->toArray();
        $city = $data['city'];
        $code = $data['code'];

        $newCity = new City();
        $newCity->setName($city);
        $newCity->setCode($code);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newCity);
        $entityManager->flush();

        /*$cityList = $cityRepository->findAll();
        $cityListJson = $serializer->serialize($cityList, 'json', ['groups' => ['city']]);
        return new JsonResponse($cityListJson, Response::HTTP_OK, [], true);*/
        return new Response();
    }

    #[Route('/admin/delete_city', name: 'delete_city')]
    public function removeCity(Request $request, CityRepository $cityRepository): Response
    {
        $data = $request->toArray();
        $city = $data['city'];
        $cityX = $cityRepository->findOneBy(['name' => $city]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($cityX);
        $entityManager->flush();

        return new Response();
    }

    //******************************************************************//
    //**************************MANAGE CAMPUS***************************//
    //******************************************************************//
    #[Route('/admin/admin_campus', name: 'admin_campus')]
    public function editCampus(CampusRepository $campusRepository): Response
    {
        $campusList = $campusRepository->findAll();
        return $this->render('admin/admin_campus.html.twig', [
            'campusList' => $campusList,
        ]);
    }

    #[Route('/admin/add_campus', name: 'add_campus')]
    public function addCampus(Request $request): Response
    {
        $data = $request->toArray();
        $campus = $data['campus'];
        $newCampus = new Campus();
        $newCampus->setName($campus);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newCampus);
        $entityManager->flush();

        return new Response();
    }

    #[Route('/admin/delete_campus', name: 'delete_campus')]
    public function removeCampus(Request $request, CampusRepository $campusRepository): Response
    {
        $data = $request->toArray();
        $campus = $data['campus'];
        $campusX = $campusRepository->findOneBy(['name' => $campus]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($campusX);
        $entityManager->flush();

        return new Response();
    }

    //******************************************************************//
    //****************************MANAGE USERS**************************//
    //******************************************************************//
    #[Route('/admin/user_admin', name: 'user_admin')]
    public function userAdmin(): Response
    {
        return $this->render('admin/user_admin.html.twig');
    }

    #[Route('/admin/admin_user', name: 'admin_user')]
    public function userManage(UserRepository $userRepository): Response
    {
        $userList = $userRepository->findAll();
        return $this->render('admin/admin_user.html.twig', [
            'userList' => $userList,
        ]);
    }

    #[Route('/admin/user_suspend', name: 'user_suspend')]
    public function userSuspend(Request $request, UserRepository $userRepository): Response
    {
        $data = $request->toArray();
        $user = $data['user'];
        $user = $userRepository->findOneBy(['name' => $user]);
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
        return new Response($this->renderView('templates/_user_table.html.twig', [
            'userList' => $userList,
        ]));
    }

    #[Route('/admin/user_delete', name: 'user_delete')]
    public function userDelete(Request $request, UserRepository $userRepository): Response
    {
        $data = $request->toArray();
        $user = $data['user'];
        $userX = $userRepository->findOneBy(['name' => $user]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($userX);
        $entityManager->flush();

        return new Response();
    }

    //******************************************************************//
    //***************************ADD NEW USERS**************************//
    //******************************************************************//
    #[Route('/admin/user_register', name: 'app_register')]
    public function userRegister(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
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
