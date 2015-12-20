<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\UntagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin UntagPost */
class UntagPostSpec extends ObjectBehavior
{
    const TEST_TAG = 'My tag';

    function let(PostId $postId)
    {
        $this->beConstructedWith($postId, self::TEST_TAG);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UntagPost::class);
    }

    function it_allows_retrieving_post_id(PostId $postId)
    {
        $this->postId()->shouldBeLike($postId);
        $this->aggregateId()->shouldBeLike($postId);
    }

    function it_allow_retrieving_tag()
    {
        $this->tagName()->shouldBeLike(self::TEST_TAG);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:untag_post');
    }
}
