<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/21/19
 * Time: 10:10 AM
 */

namespace App\Command;

use App\Controller\SubscriptionController;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronSubscription extends Command
{
    protected static $defaultName = 'app:start-cron';

    private $subscriptionController;
    private $subscriptionRepository;
    private $entityManager;

    public function __construct(
        SubscriptionController $subscriptionController,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->subscriptionController = $subscriptionController;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Starting a job')
            ->setHelp('This command checks user subscriptions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->subscriptionController->checkSubscription(
            $this->subscriptionRepository,
            $this->entityManager
        );

        $output->writeln('Checking subscription.');
    }
}
