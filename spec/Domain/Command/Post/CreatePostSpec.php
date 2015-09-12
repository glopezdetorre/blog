<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreatePostSpec extends ObjectBehavior
{
    const POST_ID = 'a54a1776-d347-4e75-8e8a-b6ebf034b912';
    const POST_TITLE = 'Title';
    const POST_CONTENT = 'Post content';

    function let()
    {
        $this->beConstructedWith(
            self::POST_ID,
            self::POST_TITLE,
            self::POST_CONTENT
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreatePost::class);
    }

    function it_should_allow_retrieving_post_id()
    {
        $this->postId()->shouldBe(self::POST_ID);
    }

    function it_should_allow_retrieving_post_title()
    {
        $this->postTitle()->shouldBe(self::POST_TITLE);
    }

    function it_should_allow_retrieving_post_content()
    {
        $this->postContent()->shouldBe(self::POST_CONTENT);
    }
}
