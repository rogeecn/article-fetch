<?php

namespace rogeecn\ArticleFetch\Classes\article;


class Author implements \rogeecn\ArticleFetch\Contracts\Author
{
    private $id;
    private $name;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        // todo: get author info
        return "todo:" . $this->id;
        return $this->name;
    }

    /**
     * @param int $count
     *
     * @return Summary[]
     */
    public function posts($count = 20)
    {
        return [
            new Summary(),
            new Summary(),
            new Summary(),
            new Summary(),
        ];
    }
}
