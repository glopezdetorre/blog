<?php

namespace Gorka\Blog\Domain\Query\Post;

use Gorka\Blog\Domain\Query\DomainQuery;

class LatestPosts implements DomainQuery
{
    public function messageName()
    {
        return 'blog:query_latest_posts';
    }
}
