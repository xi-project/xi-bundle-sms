<?php

namespace Xi\Bundle\SmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Xi\Sms\SmsMessage;


class SendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sms:send')
            ->setDescription('Sends an SMS')
            ->addArgument('to', InputArgument::REQUIRED, 'Receiver MSISDN')
            ->addArgument('from', InputArgument::REQUIRED, 'Sender identifier (string or MSISDN)')
            ->addArgument('message', InputArgument::REQUIRED, 'Message')
            ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = new SmsMessage(
            $input->getArgument('message'),
            $input->getArgument('from'),
            $input->getArgument('to')
        );

        $this->getContainer()->get('xi_sms.gateway')->send($message);
    }
}
