<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\User;
use League\Csv\Reader;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProcessCSV extends AbstractController
{
    public function processCSV($data, UserPasswordEncoderInterface $passwordEncoder)
    {
        $csv = Reader::createFromFileObject(new SplFileObject($data));
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();
        foreach ($records as $offset => $record) {
            $user = new User();
            $campus = $this->getDoctrine()
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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }
}