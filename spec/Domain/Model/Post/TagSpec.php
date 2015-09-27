<?php

namespace spec\Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Model\Post\Tag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TagSpec extends ObjectBehavior
{
    const TEST_NAME = 'My Tag';
    const TEST_SLUG = 'my-slug';

    function let()
    {
        $this->beConstructedThrough('create', [self::TEST_NAME, self::TEST_SLUG]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Tag::class);
    }

    function it_should_allow_retrieving_tag_string()
    {
        $this->name()->shouldBe(self::TEST_NAME);
    }

    function it_should_allow_retrieving_tag_slug()
    {
        $this->slug()->shouldBe(self::TEST_SLUG);
    }

    function it_should_be_castable_to_string()
    {
        $this->__toString()->shouldBe(self::TEST_NAME);
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
            $this->shouldThrow(\InvalidArgumentException::class)->during('create', [$invalidName, self::TEST_SLUG]);
        }
    }

    function it_should_not_allow_non_string_or_empty_slugs()
    {
        $invalidSlugs = [
            '',
            "\n",
            '    ',
            '--_',
            new \StdClass(),
            45
        ];

        foreach ($invalidSlugs as $invalidSlug) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('create', [self::TEST_NAME, $invalidSlug]);
        }
    }
}
