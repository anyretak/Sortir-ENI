<?php

namespace App\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

interface ProcessCSVInterface
{
    public function processCSV($data, UserPasswordEncoderInterface $passwordEncoder);
}