<?php

namespace spec\Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\UnpublishPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Ui\Console\PostUnpublish;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostUnpublishSpec extends ObjectBehavior
{
    const TEST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function let(MessageBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostUnpublish::class);
    }

    function it_should_put_unpublish_command_on_the_bus(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('id')->willReturn(self::TEST_ID);
        $message = new UnpublishPost(PostId::create(self::TEST_ID));
        $commandBus->handle($message)->shouldBeCalled();
        $output->writeln(Argument::containingString('has been unpublished'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_unpublish_command_on_invalid_id(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('id')->willReturn(null);
        $commandBus->handle(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to unpublish post'))->shouldBeCalled();
        $this->execute($input, $output);
    }
}
