<?php

namespace spec\Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\PublishPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Ui\Console\PostPublish;
use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** @mixin PostPublish */
class PostPublishSpec extends ObjectBehavior
{
    const TEST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function let(CommandBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostPublish::class);
    }

    function it_should_put_publish_command_on_the_bus(
        InputInterface $input,
        OutputInterface $output,
        CommandBus $commandBus
    ) {
        $input->getArgument('id')->willReturn(self::TEST_ID);
        $message = new PublishPost(PostId::create(self::TEST_ID));
        $commandBus->dispatch($message)->shouldBeCalled();
        $output->writeln(Argument::containingString('has been published'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_publish_command_on_invalid_id(
        InputInterface $input,
        OutputInterface $output,
        CommandBus $commandBus
    ) {
        $input->getArgument('id')->willReturn(null);
        $commandBus->dispatch(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to publish post'))->shouldBeCalled();
        $this->execute($input, $output);
    }
}
