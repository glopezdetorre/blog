<?php

namespace spec\Gorka\Blog\Infrastructure\Ui\Console;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Service\IdGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Gorka\Blog\Infrastructure\Ui\Console\PostImport;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostImportSpec extends ObjectBehavior
{
    const TEST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const TEST_FILENAME = 'test.md';
    const TEST_CONTENT = '# Test';
    const TEST_TITLE = 'Test title';

    /**
     * @var vfsStreamDirectory
     */
    private $workdir;

    function let(MessageBus $commandBus, IdGenerator $idGenerator, QuestionHelper $questionHelper)
    {
        $this->workdir = vfsStream::setup('workdir');
        $idGenerator->id()->willReturn(self::TEST_ID);
        $this->beConstructedWith($commandBus, $idGenerator, $questionHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostImport::class);
    }

    function it_should_put_create_post_command_on_the_bus(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus,
        QuestionHelper $questionHelper
    ) {
        $questionHelper->ask($input, $output, Argument::any())->willReturn(self::TEST_TITLE);
        $input->getArgument('file')->willReturn('vfs://workdir/'.self::TEST_FILENAME);
        $this->createFile(self::TEST_FILENAME, self::TEST_CONTENT);

        $message = new CreatePost(
            PostId::create(self::TEST_ID),
            self::TEST_TITLE,
            null,
            self::TEST_CONTENT
        );

        $commandBus->handle($message)->shouldBeCalled();
        $output->writeln(Argument::containingString('has been imported'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_commands_on_bus_on_file_not_found(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('file')->willReturn('vfs://workdir/'.self::TEST_FILENAME);

        $commandBus->handle(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to import post'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_should_not_put_commands_on_bus_on_file_unreaedable(
        InputInterface $input,
        OutputInterface $output,
        MessageBus $commandBus
    ) {
        $input->getArgument('file')->willReturn('vfs://workdir/'.self::TEST_FILENAME);
        $this->createFile(self::TEST_FILENAME, self::TEST_CONTENT, 0222);

        $commandBus->handle(Argument::any())->shouldNotBeCalled();
        $output->writeln(Argument::containingString('Unable to import post'))->shouldBeCalled();
        $this->execute($input, $output);
    }

    private function createFile($fileName, $content, $permissions = 0664)
    {
        $file = vfsStream::newFile($fileName);
        $file->setContent($content)->chmod($permissions);
        $this->workdir->addChild($file);
    }
}
