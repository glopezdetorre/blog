<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreatePostSpec extends ObjectBehavior
{
    const POST_TITLE = 'My title';
    const POST_SLUG = 'my-title';
    const POST_CONTENT = 'Post content';

    function let(PostId $postId)
    {
        $this->beConstructedWith(
            $postId,
            self::POST_TITLE,
            self::POST_SLUG,
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

    function it_should_allow_retrieving_post_slug()
    {
        $this->postSlug()->shouldBe(self::POST_SLUG);
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
