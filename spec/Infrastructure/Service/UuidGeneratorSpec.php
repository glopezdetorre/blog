<?php

namespace spec\Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Infrastructure\Service\UuidGenerator;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Rhumsaa\Uuid\Uuid;

class UuidGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UuidGenerator::class);
    }

    function it_should_generate_uuids()
    {
        $this->id()->shouldBeValidUuid();
        $this->id()->shouldNotBeLike($this->id());
    }

    public function getMatchers()
    {
        return [
            'beValidUuid' => function ($subject) {
                if (!Uuid::isValid($subject)) {
                    throw new FailureException();
                }
                return true;
            }
        ];
    }

}
