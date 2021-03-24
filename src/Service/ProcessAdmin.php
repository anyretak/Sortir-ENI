<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProcessAdmin implements ProcessAdminInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addCity($data)
    {
        $newCity = new City();
        $newCity->setName($data['city']);
        $newCity->setCode($data['code']);
        $entityManager = $this->entityManager;
        $entityManager->persist($newCity);
        $entityManager->flush();
    }

    public function removeCity($data)
    {
        $city = $this->entityManager
            ->getRepository(City::class)
            ->findOneBy(['name' => $data['city']]);
        $entityManager = $this->entityManager;
        $entityManager->remove($city);
        $entityManager->flush();
    }

    public function addCampus($data)
    {
        $newCampus = new Campus();
        $newCampus->setName($data['campus']);
        $entityManager = $this->entityManager;
        $entityManager->persist($newCampus);
        $entityManager->flush();
    }

    public function removeCampus($data)
    {
        $campus = $this->entityManager
            ->getRepository(Campus::class)
            ->findOneBy(['name' => $data['campus']]);
        $entityManager = $this->entityManager;
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
        $entityManager = $this->entityManager;
        $entityManager->persist($user);
        $entityManager->flush();
        return $userRepository->findAll();
    }

    public function deleteUser($data)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['name' => $data['user']]);
        $entityManager = $this->entityManager;
        $entityManager->remove($user);
        $entityManager->flush();
    }
}