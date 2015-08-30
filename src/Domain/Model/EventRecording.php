<?php

namespace Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;

interface EventRecording
{
    public function recordedEvents();

    public function recordThat(DomainEvent $event);

    public static function reconstituteFromEvents(AggregateHistory $aggregateHistory);
}
