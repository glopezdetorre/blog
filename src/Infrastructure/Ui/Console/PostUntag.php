<?php

namespace Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\UntagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use Gorka\Blog\Domain\Service\Slugifier;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostUntag extends Command
{
    /**
     * @var MessageBus Message bus
     */
    private $commandBus;

    /**
     * @var Slugifier
     */
    private $slugifier;

    /**
     * @param MessageBus $commandBus
     * @param Slugifier $slugifier
     */
    public function __construct(MessageBus $commandBus, Slugifier $slugifier)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->slugifier = $slugifier;
    }

    protected function configure()
    {
        $this
            ->setName('post:untag')
            ->setDescription('Untag post')
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
            $this->commandBus->handle(new UntagPost($id, $tag));
            $output->writeln(sprintf('<info>Post with id %s has been untagged \'%s\'</info>', $id, $tag));
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to untag post:</error> '.$e->getMessage());
        }
    }
}
