<?php

namespace App\Service;

interface ProcessMapInterface
{
    public function processMap($data, ProcessFiltersInterface $processFilters);
}