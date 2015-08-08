<?php

namespace spec\Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Rhumsaa\Uuid\Uuid;

class PostIdSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PostId::class);
    }

    function it_should_not_allow_non_uuid_or_blank_ids()
    {
        $invalidValues = [
            '',
            null,
            23,
            'this_is_not_a_uuid',
            new \StdClass()
        ];

        /** @see https://github.com/phpspec/phpspec/pull/699 */
        foreach ($invalidValues as $value) {
            $this->beConstructedThrough('create', [$value]);
            try {
                $this->getWrappedObject();
                throw new \RuntimeException($value);
            } catch (\InvalidArgumentException $e) {}
        }
    }

    function it_should_be_castable_to_string()
    {
        $uuid = Uuid::uuid4();
        $this->beConstructedThrough('create', [$uuid->toString()]);
        $this->__toString()->shouldBe($uuid->toString());
    }
}
