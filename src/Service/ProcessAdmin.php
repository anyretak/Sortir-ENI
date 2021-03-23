<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProcessAdmin extends AbstractController
{
    public function addCity($data)
    {
        $newCity = new City();
        $newCity->setName($data['city']);
        $newCity->setCode($data['code']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newCity);
        $entityManager->flush();
    }

    public function removeCity($data)
    {
        $city = $this->getDoctrine()
            ->getRepository(City::class)
            ->findOneBy(['name' => $data['city']]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($city);
        $entityManager->flush();
    }

    public function addCampus($data)
    {
        $newCampus = new Campus();
        $newCampus->setName($data['campus']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newCampus);
        $entityManager->flush();
    }

    public function removeCampus($data)
    {
        $campus = $this->getDoctrine()
            ->getRepository(Campus::class)
            ->findOneBy(['name' => $data['campus']]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($campus);
        $entityManager->flush();
    }

    public function suspendUser($data, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['name' => $data['user']]);
        $userStatus = $user->getIsActive();
        if ($userStatus == "") {
            $user->setIsActive(true);
        } else {
            $user->setIsActive(false);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $userRepository->findAll();
    }

    public function deleteUser($data)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['name' => $data['user']]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }
}