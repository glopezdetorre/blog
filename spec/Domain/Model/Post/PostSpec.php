<?php

namespace spec\Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostSpec extends ObjectBehavior
{
    const TEST_CONTENT = 'My content';
    const TEST_TITLE = 'My title';
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function let()
    {
        $this->beConstructedThrough(
            'create', [
                PostId::create(self::POST_ID),
                self::TEST_TITLE,
                self::TEST_CONTENT
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Post::class);
    }

    function it_should_have_an_id()
    {
        $this->id()->shouldBeLike(PostId::create(self::POST_ID));
    }

    function it_should_record_title_changes()
    {

    }

    function it_should_not_allow_non_string_titles()
    {
        $badTitles = [
            null,
            new \StdClass(),
            true,
            123
        ];

        foreach ($badTitles as $badTitle) {
            $this
                ->shouldThrow(\InvalidArgumentException::class)
                ->during('create', [PostId::create(self::POST_ID), $badTitle, self::TEST_CONTENT]);
        }
    }

    function it_should_not_allow_empty_titles()
    {
        $emptyTitles = [
            '',
            '   ',
            "\t",
            "\n",
            "\t  \n"
        ];

        foreach ($emptyTitles as $emptyTitle) {
            $this
                ->shouldThrow(\InvalidArgumentException::class)
                ->during('create', [PostId::create(self::POST_ID), $emptyTitle, self::TEST_CONTENT]);
        }
    }

    function it_should_allow_changing_its_content()
    {

    }

    function it_should_not_allow_non_stringable_content()
    {
        $badValues = [
            null,
            new \StdClass(),
            true,
            123
        ];

        foreach ($badValues as $badValue) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('changeContent', [$badValue]);
        }
    }

    function it_should_allow_chaging_its_slug()
    {

    }

    function it_should_not_allow_non_string_or_empty_slugs()
    {
        $badSlugs = [
            null,
            new \StdClass(),
            true,
            123,
            '  ',
            "\n",
            "  \t ",
            ''
        ];

        foreach ($badSlugs as $badSlug) {
            $this->shouldThrow(\InvalidArgumentException::class)->during('changeSlug', [$badSlug]);
        }
    }

    function it_sould_allow_changing_publishing_status()
    {

    }
}
