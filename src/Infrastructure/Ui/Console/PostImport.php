<?php

namespace Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Service\IdGenerator;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PostImport extends Command
{
    /**
     * @var MessageBus Message bus
     */
    private $commandBus;

    /**
     * @var IdGenerator User ID generator
     */
    private $userIdGenerator;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @param CommandBus $commandBus
     * @param IdGenerator $userIdGenerator
     * @param QuestionHelper $questionHelper
     */
    public function __construct(CommandBus $commandBus, IdGenerator $userIdGenerator, QuestionHelper $questionHelper)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->userIdGenerator = $userIdGenerator;
        $this->questionHelper = $questionHelper;
    }

    protected function configure()
    {
        $this
            ->setName('post:import')
            ->setDescription('Import post from file')
            ->addArgument('file', InputArgument::REQUIRED, 'File to import');
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = $input->getArgument('file');

        try {
            if (!file_exists($fileName)) {
                throw new \InvalidArgumentException('File not found');
            }

            if (!is_readable($fileName)) {
                throw new \InvalidArgumentException('File cannot be read');
            }

            $id = PostId::create($this->userIdGenerator->id());
            $content = file_get_contents($fileName);
            $title = $this->questionHelper->ask($input, $output, new Question('Please enter title for this post: '));

            $this->commandBus->dispatch(new CreatePost($id, $title, null, $content));

            // This might not be true: we have put the command on the bus,
            // there is no guarantee is has been accomplished
            $output->writeln(sprintf('<info>Post with id %s and title %s created</info>', $id, $title));
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to import post:</error> '.$e->getMessage());
        }
    }
}
