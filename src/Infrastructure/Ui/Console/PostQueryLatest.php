<?php

namespace Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Query\Post\LatestPosts;
use Prooph\ServiceBus\QueryBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostQueryLatest extends Command
{
    /**
     * @var QueryBus Message bus
     */
    private $queryBus;

    /**
     * @param QueryBus $queryBus
     */
    public function __construct(QueryBus $queryBus)
    {
        parent::__construct();
        $this->queryBus = $queryBus;
    }

    protected function configure()
    {
        $this
            ->setName('post:queryLatest')
            ->setDescription('Get latest posts')
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
            $received = null;
            $promise = $this->queryBus->dispatch(new LatestPosts());
            $promise
                ->then(
                    function ($response) use (&$received) {
                        $received = $response;
                    }
                );
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to query latest postst:</error> '.$e->getMessage());
        }
    }
}
