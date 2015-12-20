<?php

namespace spec\Gorka\Blog\Infrastructure\Service;

use Gorka\Blog\Infrastructure\Service\SystemClock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin SystemClock */
class SystemClockSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SystemClock::class);
    }

    function it_should_return_current_time()
    {
        $this
            ->now(new \DateTimeZone('Europe/Madrid'))
            ->shouldBeLike(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Madrid')));
    }
}
