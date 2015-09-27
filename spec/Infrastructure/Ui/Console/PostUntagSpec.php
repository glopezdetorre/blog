<?php

namespace spec\Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\UntagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use Gorka\Blog\Domain\Service\Slugifier;
use Gorka\Blog\Infrastructure\Ui\Console\PostUntag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostUntagSpec extends ObjectBehavior
{
    const TEST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const TEST_TAG_NAME = 'My tag';
    const TEST_TAG_SLUG = 'my-tag';

    function let(MessageBus $commandBus, Slugifier $slugifier)
    {
        $this->beConstructedWith($commandBus, $slugifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostUntag::class);
    }

    function it_should_put_publish_command_on_the_bus(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus,
        Slugifier $slugifier
    ) {
        $input->getArgument('id')->willReturn(self::TEST_ID);
        $input->getArgument('tag')->willReturn(self::TEST_TAG_NAME);
        $slugifier->slugify(self::TEST_TAG_NAME)->willReturn(self::TEST_TAG_SLUG);
        $message = new UntagPost(PostId::create(self::TEST_ID), Tag::create(self::TEST_TAG_NAME, self::TEST_TAG_SLUG));
        $commandBus->handle($message)->shouldBeCalled();
        $output->writeln(Argument::containingString('has been untagged'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_publish_command_on_invalid_id(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('id')->willReturn(null);
        $commandBus->handle(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to untag post'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_publish_command_on_invalid_tag(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('tag')->willReturn(null);
        $commandBus->handle(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to untag post'))->shouldBeCalled();
        $this->execute($input, $output);
    }
}
