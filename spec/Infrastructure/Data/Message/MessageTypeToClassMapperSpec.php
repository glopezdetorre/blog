<?php

namespace spec\Gorka\Blog\Infrastructure\Data\Message;

use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Infrastructure\Data\Message\MessageTypeToClassMapper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageTypeToClassMapperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MessageTypeToClassMapper::class);
    }

    public function it_should_not_be_initializable_with_invalid_maps()
    {
        $invalidMaps = [
            5,
            'wrong',
            new \StdClass(),
            ['unknown'],
            ['unknown' => [45]],
            ['unknown' => 'UnexistingClass'],
            [
                'PostWasCreated' => PostWasCreated::class,
                'unknown' => null
            ]
        ];

        foreach ($invalidMaps as $invalidMap) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$invalidMap]);
        }
    }

    public function it_should_be_initializable_with_map()
    {
        $this->beConstructedWith([
            'PostWasCreated' => PostWasCreated::class,
            'PostWasPublished' => PostWasPublished::class
        ]);
        $this->classFromMessageType('PostWasCreated')->shouldBe(PostWasCreated::class);
        $this->classFromMessageType('PostWasPublished')->shouldBe(PostWasPublished::class);
        $this->messageTypeFromClass(PostWasCreated::class)->shouldBe('PostWasCreated');
        $this->messageTypeFromClass(PostWasPublished::class)->shouldBe('PostWasPublished');
    }

    public function it_should_throw_exception_on_class_not_found()
    {
        $this->shouldThrow(\LogicException::class)->during('classFromMessageType', ['Unknown']);
    }

    public function it_should_throw_exception_on_message_type_not_found()
    {
        $this->shouldThrow(\LogicException::class)->during('messageTypeFromClass', ['Unknown']);
    }

    public function it_should_allow_adding_new_maps()
    {
        $this->add('PostWasPublished', PostWasPublished::class);
        $this->messageTypeFromClass(PostWasPublished::class)->shouldBe('PostWasPublished');
        $this->classFromMessageType('PostWasPublished')->shouldBe(PostWasPublished::class);
    }

    public function it_should_guard_added_mappings()
    {
        $invalidMappings = [
            [null, null],
            [5, PostWasPublished::class],
            ['unknown', null],
            ['unknown', 5],
            ['unknwon', 'UnknownClass']
        ];

        foreach ($invalidMappings as $invalidMapping) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('add', $invalidMapping);
        }
    }
}
