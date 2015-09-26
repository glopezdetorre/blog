<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostContentWasChanged;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostContentWasChangedSpec extends ObjectBehavior
{
    const POST_CONTENT = 'New content';

    function let(PostId $postId)
    {
        $this->beConstructedWith($postId, self::POST_CONTENT);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostContentWasChanged::class);
    }

    function it_should_allow_getting_post_id(PostId $postId)
    {
        $this->aggregateId()->shouldBeLike($postId);
        $this->postId()->shouldBeLike($postId);
    }

    function it_should_allow_getting_post_content()
    {
        $this->postContent()->shouldBeLike(self::POST_CONTENT);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:post_content_was_changed');
    }
}
