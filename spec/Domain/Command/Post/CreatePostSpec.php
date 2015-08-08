<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreatePostSpec extends ObjectBehavior
{
    const POST_TITLE = 'Title';
    const POST_DATE_STRING = '2015-03-07 23:00:15';
    const POST_CONTENT = 'Post content';

    function let()
    {
        $this->beConstructedWith(
            self::POST_TITLE,
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', self::POST_DATE_STRING),
            self::POST_CONTENT
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreatePost::class);
    }

    function it_should_allow_retrieving_post_title()
    {
        $this->postTitle()->shouldBe(self::POST_TITLE);
    }

    function it_should_allow_retrieving_post_creation_date()
    {
        $this
            ->postCreationDateTime()
            ->shouldBeLike(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', self::POST_DATE_STRING));
    }

    function it_should_allow_retrieving_post_content()
    {
        $this->postContent()->shouldBe(self::POST_CONTENT);
    }
}
