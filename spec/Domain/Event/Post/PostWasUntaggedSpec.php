<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostWasUntagged;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostWasUntaggedSpec extends ObjectBehavior
{
    function let(PostId $postId, Tag $tag)
    {
        $this->beConstructedWith($postId, $tag);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostWasUntagged::class);
    }

    function it_allows_retrieving_post_id(PostId $postId)
    {
        $this->postId()->shouldBeLike($postId);
        $this->aggregateId()->shouldBeLike($postId);
    }

    function it_allow_retrieving_tag(Tag $tag)
    {
        $this->tag()->shouldBeLike($tag);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:post_was_untagged');
    }
}
