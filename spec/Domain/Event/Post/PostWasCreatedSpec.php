<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin PostWasCreated */
class PostWasCreatedSpec extends ObjectBehavior
{
    const TEST_CONTENT = 'My content';
    const TEST_TITLE = 'My title';
    const TEST_SLUG = 'My title';

    function let(PostId $postId)
    {
        $this->beConstructedWith($postId, self::TEST_TITLE, self::TEST_SLUG, self::TEST_CONTENT);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostWasCreated::class);
    }

    function it_should_allow_getting_post_id(PostId $postId)
    {
        $this->aggregateId()->shouldBeLike($postId);
        $this->postId()->shouldBeLike($postId);
    }

    function it_should_allow_getting_post_title()
    {
        $this->postTitle()->shouldBe(self::TEST_TITLE);
    }

    function it_should_allow_getting_post_slug()
    {
        $this->postSlug()->shouldBe(self::TEST_SLUG);
    }

    function it_should_allow_getting_post_content()
    {
        $this->postContent()->shouldBe(self::TEST_CONTENT);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:post_was_created');
    }
}
