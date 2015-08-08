<?php

namespace spec\Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostSpec extends ObjectBehavior
{
    const CREATION_DATE_STRING = '2015-02-15 23:00:00';
    const TITLE = 'Title';
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    /** @var \DateTimeImmutable */
    private $testDate;

    function let()
    {
        $this->testDate = \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            self::CREATION_DATE_STRING
        );

        $this->beConstructedWith(
            PostId::create(self::POST_ID),
            self::TITLE,
            $this->testDate
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

    function it_should_have_a_title()
    {
        $this->title()->shouldBe(self::TITLE);
    }

    function it_should_have_a_creation_date()
    {
        $this->creationDateTime()->shouldBeLike($this->testDate);
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
                ->during('__construct', [PostId::create(self::POST_ID), $badTitle, $this->testDate]);
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
                ->during('__construct', [PostId::create(self::POST_ID), $emptyTitle, $this->testDate]);
        }
    }

    function it_should_allow_changing_its_content()
    {
        $content = "Test content";
        $this->changeContent($content);
        $this->content()->shouldBe($content);
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
        $slug = "new-slug-post";
        $this->changeSlug($slug);
        $this->slug()->shouldBe($slug);
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

    function it_should_not_be_published_by_default()
    {
        $this->published()->shouldBe(false);
    }

    function it_sould_allow_changing_publishing_status()
    {
        $this->published()->shouldBe(false);
        $this->publish();
        $this->published()->shouldBe(true);
        $this->unpublish();
        $this->published()->shouldBe(false);
    }
}
