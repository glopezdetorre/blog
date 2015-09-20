<?php

namespace spec\Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\Post\PublishPost;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PublishPostSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';

    function let()
    {
        $this->beConstructedWith(PostId::create(self::POST_ID));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PublishPost::class);
    }

    function it_should_allow_getting_post_id_to_publish()
    {
        $this->postId()->shouldBeLike(self::POST_ID);
    }

    function it_should_allow_retrieving_message_name()
    {
        $this->messageName()->shouldBe('blog:publish_post');
    }
}
