<?php

namespace Xi\Bundle\SmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class SendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sms:send')
            ->setDescription('Sends an SMS')
            ->addOption('to', 't', InputOption::VALUE_REQUIRED, 'Receiver MSISDN')
            ->addOption('from', 'f', InputOption::VALUE_REQUIRED, 'Sender identifier (string or MSISDN)')
            ->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Message')
            ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {



    }
}
