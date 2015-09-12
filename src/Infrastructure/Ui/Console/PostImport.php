<?php

namespace Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Service\UuidGenerator;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostImport extends Command
{
    /**
     * @var MessageBus Message bus
     */
    private $commandBus;

    /**
     * @var UuidGenerator User ID generator
     */
    private $userIdGenerator;

    /**
     * @param MessageBus $commandBus
     * @param UuidGenerator $userIdGenerator
     */
    public function __construct(MessageBus $commandBus, UuidGenerator $userIdGenerator)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->userIdGenerator = $userIdGenerator;
    }

    protected function configure()
    {
        $this
            ->setName('post:import')
            ->setDescription('Import post from markdown file')
            ->addArgument('title', InputArgument::REQUIRED, 'Post title')
            ->addArgument('file', InputArgument::REQUIRED, 'Post content')
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
            $file = $input->getArgument('file');
            if (file_exists($file) && is_readable($file)) {
                $content = file_get_contents($file);
            } else {
                throw new \InvalidArgumentException('File not found or unable to read file');
            }

            $id = PostId::create($this->userIdGenerator->id());
            $title = $input->getArgument('title');
            $this->commandBus->handle(new CreatePost($id, $title, $content));
            $output->writeln(sprintf('<info>Post with id %s and title %s created</info>', $id, $title));
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to import post:</error> '.$e->getMessage());
        }
    }
}
