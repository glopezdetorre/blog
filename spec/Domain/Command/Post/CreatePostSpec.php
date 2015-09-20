<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreatePostSpec extends ObjectBehavior
{
    const POST_TITLE = 'Title';
    const POST_CONTENT = 'Post content';
    const POST_ID = 'a54a1776-d347-4e75-8e8a-b6ebf034b912';

    function let(PostId $postId)
    {
        $postId->id()->willReturn(self::POST_ID);
        $postId->__toString()->willReturn(self::POST_ID);

        $this->beConstructedWith(
            $postId,
            self::POST_TITLE,
            self::POST_CONTENT
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreatePost::class);
    }

    function it_should_allow_retrieving_post_id(PostId $postId)
    {
        $this->postId()->shouldBeLike($postId);
    }

    function it_should_allow_retrieving_post_title()
    {
        $this->postTitle()->shouldBe(self::POST_TITLE);
    }

    function it_should_allow_retrieving_post_content()
    {
        $this->postContent()->shouldBe(self::POST_CONTENT);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:create_post');
    }
}
