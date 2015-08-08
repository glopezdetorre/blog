<?php

namespace Gorka\Blog\Domain\Model\Post;

interface PostRepository
{
    /**
     * @return PostId
     */
    public function nextIdentity();

    /**
     * @param PostId $id
     * @return Post
     */
    public function byId(PostId $id);

    /**
     * @param Post $post
     * @return mixed
     */
    public function add(Post $post);

    /**
     * @param string $slug
     * @return Post
     */
    public function bySlug($slug);
}