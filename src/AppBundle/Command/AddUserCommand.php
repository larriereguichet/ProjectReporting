<?php

namespace AppBundle\Command;

use AppBundle\Entity\George;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class AddUserCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('guard:user:add')
            ->setDescription('Add a user in database')
            ->addArgument('username', InputArgument::REQUIRED, 'User name')
            ->addArgument('password', InputArgument::REQUIRED, 'User password. It will be encoded')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $george = new George();
        $george->setUsername($input->getArgument('username'));
        $george->setEmail($input->getArgument('email'));
        $george->setRoles([
            'ROLE_USER'
        ]);
        $george->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $encoded = $this
            ->container
            ->get('security.password_encoder')
            ->encodePassword($george, $input->getArgument('password'));
        $george->setPassword($encoded);

        $this
            ->container
            ->get('lag.george_repository')
            ->save($george);
    }
}
