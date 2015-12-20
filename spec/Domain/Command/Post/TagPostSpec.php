<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin TagPost */
class TagPostSpec extends ObjectBehavior
{
    const TEST_TAG = 'My tag';

    function let(PostId $postId)
    {
        $this->beConstructedWith($postId, self::TEST_TAG);
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

    function it_allows_retrieving_tag()
    {
        $this->tagName()->shouldBeLike(self::TEST_TAG);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:tag_post');
    }
}
