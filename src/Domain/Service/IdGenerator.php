<?php

namespace Gorka\Blog\Domain\Service;

interface IdGenerator
{
    /**
     * @return mixed
     */
    public function id();
}
