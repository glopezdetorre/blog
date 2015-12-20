<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\UnpublishPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin UnpublishPost */
class UnpublishPostSpec extends ObjectBehavior
{
    function let(PostId $postId)
    {
        $this->beConstructedWith($postId);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UnpublishPost::class);
    }

    function it_should_allow_getting_post_id_to_publish(PostId $postId)
    {
        $this->postId()->shouldBeLike($postId);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:unpublish_post');
    }
}
