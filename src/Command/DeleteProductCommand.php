<?php

namespace App\Command;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'DeleteProductCommand',
    description: 'Add a short description for your command',
)]
class DeleteProductCommand extends Command
{

    public function __construct(
        
        private ProductRepository $productRepository,
        private EntityManagerInterface $em
    )
    { parent::__construct(); }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');
        $nbProduct = 0;

        $productEntities = $this->productRepository->getAllNonActiveProducts();
        foreach($productEntities as $product){
            $nbProduct++;
            $this->em->remove($product);
        }
        $this->em->flush();

        $output->writeln("$nbProduct produit(s) on été supprimé");

        return Command::SUCCESS;
    }
}
