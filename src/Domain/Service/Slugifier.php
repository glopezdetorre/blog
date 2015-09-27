<?php

namespace Gorka\Blog\Domain\Service;

interface Slugifier
{
    public function slugify($string, $separator = '-');
}
