<?php

namespace App\Service;

interface ProcessFiltersInterface
{
    public function processFilters($filters, $userSubs = []);

    public function archiveDate();
}