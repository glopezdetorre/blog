<?php

namespace Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostTag extends Command
{
    /**
     * @var MessageBus Message bus
     */
    private $commandBus;

    /**
     * @param MessageBus $commandBus
     */
    public function __construct(MessageBus $commandBus)
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
            $tag = Tag::create($input->getArgument('tag'));
            $this->commandBus->handle(new TagPost($id, $tag));

            $output->writeln(sprintf('<info>Post with id %s has been tagged with \'%s\'</info>', $id, $tag));
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to tag post:</error> '.$e->getMessage());
        }
    }
}