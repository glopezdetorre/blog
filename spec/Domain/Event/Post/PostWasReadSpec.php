<?php

namespace spec\Gorka\Blog\Domain\Event\Post;

use Gorka\Blog\Domain\Event\Post\PostWasRead;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostWasReadSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function let()
    {
        $this->beConstructedWith(PostId::create(self::POST_ID));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostWasRead::class);
    }

    function it_should_allow_getting_post_id()
    {
        $this->aggregateId()->shouldBeLike(self::POST_ID);
    }
}
