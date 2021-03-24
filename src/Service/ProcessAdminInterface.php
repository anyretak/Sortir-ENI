<?php

namespace App\Service;

use App\Repository\UserRepository;

interface ProcessAdminInterface
{
    public function addCity($data);

    public function removeCity($data);

    public function addCampus($data);

    public function removeCampus($data);

    public function suspendUser($data, UserRepository $userRepository);

    public function deleteUser($data);
}