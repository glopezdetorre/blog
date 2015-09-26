<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TagPostSpec extends ObjectBehavior
{
    function let(PostId $postId, Tag $tag)
    {
        $this->beConstructedWith($postId, $tag);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TagPost::class);
    }

    function it_allow_retrieving_post_id(PostId $postId)
    {
        $this->postId()->shouldBeLike($postId);
        $this->aggregateId()->shouldBeLike($postId);
    }

    function it_allows_retrieving_tag(Tag $tag)
    {
        $this->tag()->shouldBeLike($tag);
    }
}
