<?php

namespace App\Command;

use App\Service\BatchStatus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BatchStatusCommand extends Command
{
    protected static $defaultName = 'app:batch-status';
    protected static $defaultDescription = 'Batch update the status of the events';

    private $batchStatus;

    public function __construct(BatchStatus $batchStatus)
    {
        $this->batchStatus = $batchStatus;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->batchStatus->batchStatus($eventList = []);
        return Command::SUCCESS;
    }
}
