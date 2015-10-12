<?php

namespace Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\PublishPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostPublish extends Command
{
    /**
     * @var CommandBus Message bus
     */
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
            ->setName('post:publish')
            ->setDescription('Publish post')
            ->addArgument('id', InputArgument::REQUIRED, 'Post id')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $id = PostId::create($input->getArgument('id'));
            $this->commandBus->dispatch(new PublishPost($id));

            // This might not be true: we have put the command on the bus,
            // there is no guarantee is has been accomplished
            $output->writeln(sprintf('<info>Post with id %s has been published</info>', $id));
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to publish post:</error> '.$e->getMessage());
        }
    }
}
