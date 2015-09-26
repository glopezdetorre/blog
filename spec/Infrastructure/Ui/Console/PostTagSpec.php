<?php

namespace spec\Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use Gorka\Blog\Infrastructure\Ui\Console\PostTag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostTagSpec extends ObjectBehavior
{
    const TEST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const TEST_TAG = 'My tag';

    function let(MessageBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostTag::class);
    }

    function it_should_put_publish_command_on_the_bus(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('id')->willReturn(self::TEST_ID);
        $input->getArgument('tag')->willReturn(self::TEST_TAG);
        $message = new TagPost(PostId::create(self::TEST_ID), Tag::create(self::TEST_TAG));
        $commandBus->handle($message)->shouldBeCalled();
        $output->writeln(Argument::containingString('has been tagged'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_publish_command_on_invalid_id(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('id')->willReturn(null);
        $commandBus->handle(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to tag post'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_publish_command_on_invalid_tag(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('tag')->willReturn(null);
        $commandBus->handle(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to tag post'))->shouldBeCalled();
        $this->execute($input, $output);
    }
}
