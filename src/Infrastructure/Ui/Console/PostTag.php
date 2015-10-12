<?php

namespace Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostTag extends Command
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
            ->setName('post:tag')
            ->setDescription('Tag post')
            ->addArgument('id', InputArgument::REQUIRED, 'Post id')
            ->addArgument('tag', InputArgument::REQUIRED, 'Tag')
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
            $tag = $input->getArgument('tag');
            $this->commandBus->dispatch(new TagPost($id, $tag));
            $output->writeln(sprintf('<info>Post with id %s has been tagged with \'%s\'</info>', $id, $tag));
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to tag post:</error> '.$e->getMessage());
        }
    }
}
