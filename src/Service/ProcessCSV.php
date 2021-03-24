<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use SplFileObject;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProcessCSV implements ProcessCSVInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processCSV($data, UserPasswordEncoderInterface $passwordEncoder)
    {
        $csv = Reader::createFromFileObject(new SplFileObject($data));
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();
        foreach ($records as $offset => $record) {
            $user = new User();
            $campus = $this->entityManager
                ->getRepository(Campus::class)
                ->findOneBy(['name' => $record ['campus']]);
            $user->setCampus($campus);
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
            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }
}