<?php


namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:hello')
            ->setDescription('Say hello')
            ->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var string $name
         */
        $name = $input->getArgument('name');

        $output->writeln([
            'Hello from app',
            '==============',
            ''
        ]);

        $output->writeln('Hello, ' . $name);
    }
}