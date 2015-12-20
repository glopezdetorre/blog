<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin PostTitleWasChanged */
class PostTitleWasChangedSpec extends ObjectBehavior
{
    const POST_TITLE = 'New title';

    function let(PostId $postId)
    {
        $this->beConstructedWith($postId, self::POST_TITLE);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostTitleWasChanged::class);
    }

    function it_should_allow_getting_post_id(PostId $postId)
    {
        $this->aggregateId()->shouldBeLike($postId);
        $this->postId()->shouldBeLike($postId);
    }

    function it_should_allow_getting_post_title()
    {
        $this->postTitle()->shouldBeLike(self::POST_TITLE);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:post_title_was_changed');
    }
}
