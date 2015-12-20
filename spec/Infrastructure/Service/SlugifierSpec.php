<?php

namespace spec\Gorka\Blog\Infrastructure\Service;

use Cocur\Slugify\Slugify;
use Gorka\Blog\Infrastructure\Service\Slugifier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin Slugifier */
class SlugifierSpec extends ObjectBehavior
{
    const TEST_STRING = 'test: this is a strängêr\'s string';
    const TEST_SLUG = 'test_this_is_a_strangers_string';

    const TEST_SEPARATOR = '_';

    function let(Slugify $slugify)
    {
        $this->beConstructedWith($slugify);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Slugifier::class);
    }

    function it_should_slugify_strings(Slugify $slugify)
    {
        $slugify->slugify(self::TEST_STRING, self::TEST_SEPARATOR)->willReturn(self::TEST_SLUG);
        $this->slugify(self::TEST_STRING, self::TEST_SEPARATOR)->shouldBe(self::TEST_SLUG);
    }
}
