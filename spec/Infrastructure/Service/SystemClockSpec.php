<?php

namespace spec\Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Infrastructure\Service\SystemClock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SystemClockSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SystemClock::class);
    }
}
