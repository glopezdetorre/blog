<?php

namespace spec\Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Model\Post\Tag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TagSpec extends ObjectBehavior
{
    const TEST_TAG_NAME = 'My Tag';

    function let()
    {
        $this->beConstructedThrough('create', [self::TEST_TAG_NAME]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Tag::class);
    }

    function it_should_allow_retrieving_tag_string()
    {
        $this->name()->shouldBe(self::TEST_TAG_NAME);
    }

    function it_should_be_castable_to_string()
    {
        $this->__toString()->shouldBe(self::TEST_TAG_NAME);
    }

    function it_should_not_allow_non_string_or_empty_names()
    {
        $invalidNames = [
            '',
            "\n",
            '    ',
            new \StdClass(),
            45
        ];

        foreach ($invalidNames as $invalidName) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('create', [$invalidName]);
        }
    }
}
