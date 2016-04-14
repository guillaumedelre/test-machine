<?php

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoreCategorySyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('core:category:sync')
            ->setDescription('Synchronize category with data pack files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $certifiedCategories = $this->getContainer()->get('core.service.certificationy')->getCategories();
        $added = $this->getContainer()->get('core.repository.category')->sync($certifiedCategories);

        $output->writeln('Nouvelle(s) catégorie(s) : ' . $added);
    }

}
