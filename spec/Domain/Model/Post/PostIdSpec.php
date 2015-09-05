<?php

namespace spec\Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Exception\Post\InvalidPostIdException;
use Gorka\Blog\Domain\Model\Post\PostId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostIdSpec extends ObjectBehavior
{
    const TEST_UUID = '56c57e60-32e3-4e6a-93cf-87143daef5b8';

    function let()
    {
        $this->beConstructedThrough('create', [self::TEST_UUID]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostId::class);
    }

    function it_should_allow_retrieving_id()
    {
        $this->id()->shouldBeLike(self::TEST_UUID);
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
            } catch (InvalidPostIdException $e) {}
        }
    }

    function it_should_be_castable_to_string()
    {
        $this->beConstructedThrough('create', [self::TEST_UUID]);
        $this->__toString()->shouldBe(self::TEST_UUID);
    }
}
