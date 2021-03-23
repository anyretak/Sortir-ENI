<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Service\ProcessAdmin;
use App\Service\ProcessCSV;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    //**************************MANAGE CITY*****************************//
    #[Route('/admin/admin_city', name: 'admin_city')]
    public function editCity(CityRepository $cityRepository): Response
    {
        $cityList = $cityRepository->findAll();
        return $this->render('admin/admin_city.html.twig', [
            'cityList' => $cityList,
        ]);
    }

    #[Route('/admin/add_city', name: 'add_city')]
    public function addCity(Request $request, ProcessAdmin $processAdmin): Response
    {
        $data = $request->toArray();
        $processAdmin->addCity($data);
        return new Response();
    }

    #[Route('/admin/delete_city', name: 'delete_city')]
    public function removeCity(Request $request, ProcessAdmin $processAdmin): Response
    {
        $data = $request->toArray();
        $processAdmin->removeCity($data);
        return new Response();
    }

    //**************************MANAGE CAMPUS***************************//
    #[Route('/admin/admin_campus', name: 'admin_campus')]
    public function editCampus(CampusRepository $campusRepository): Response
    {
        $campusList = $campusRepository->findAll();
        return $this->render('admin/admin_campus.html.twig', [
            'campusList' => $campusList,
        ]);
    }

    #[Route('/admin/add_campus', name: 'add_campus')]
    public function addCampus(Request $request, ProcessAdmin $processAdmin): Response
    {
        $data = $request->toArray();
        $processAdmin->addCampus($data);
        return new Response();
    }

    #[Route('/admin/delete_campus', name: 'delete_campus')]
    public function removeCampus(Request $request, ProcessAdmin $processAdmin): Response
    {
        $data = $request->toArray();
        $processAdmin->removeCampus($data);
        return new Response();
    }

    //****************************MANAGE USERS**************************//
    #[Route('/admin/admin_user', name: 'admin_user')]
    public function userAdmin(): Response
    {
        return $this->render('admin/admin_user.html.twig');
    }

    #[Route('/admin/user_manage', name: 'user_manage')]
    public function userManage(UserRepository $userRepository): Response
    {
        $userList = $userRepository->findAll();
        return $this->render('admin/user_manage.html.twig', [
            'userList' => $userList,
        ]);
    }

    #[Route('/admin/user_suspend', name: 'user_suspend')]
    public function userSuspend(Request $request, ProcessAdmin $processAdmin, UserRepository $userRepository): Response
    {
        $data = $request->toArray();
        $userList = $processAdmin->suspendUser($data, $userRepository);
        return new Response($this->renderView('templates/_user_table.html.twig', [
            'userList' => $userList,
        ]));
    }

    #[Route('/admin/user_delete', name: 'user_delete')]
    public function userDelete(Request $request, ProcessAdmin $processAdmin): Response
    {
        $data = $request->toArray();
        $processAdmin->deleteUser($data);
        return new Response();
    }

    //***************************ADD NEW USERS**************************//
    #[Route('/admin/user_register', name: 'app_register')]
    public function userRegister(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                'New user successfully registered!',
            );
            return $this->render('admin/admin_user.html.twig');
        }

        return $this->render('admin/user_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/user_register_csv', name: 'user_register_csv')]
    public function userCSV(): Response
    {
        return $this->render('admin/user_register_csv.html.twig');
    }

    #[Route('/admin/csv_upload', name: 'csv_upload')]
    public function testCSV(Request $request, ProcessCSV $processCSV, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $data = $request->files->get('csv');
        $processCSV->processCSV($data, $passwordEncoder);
        $this->addFlash(
            'notice',
            'User group was successfully registered!',
        );
        return $this->render('admin/user_register_csv.html.twig');
    }
}
