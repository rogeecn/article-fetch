<?php

namespace rogeecn\ArticleFetch\Classes\article;


class Summary implements \rogeecn\ArticleFetch\Contracts\Summary
{
    private $id;
    private $author_id;
    private $category_id;
    private $title;
    private $description;
    private $head_image;
    private $source_url;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->author_id = $data['author_id'];
        $this->category_id = $data['category_id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->head_image = $data['head_image'];
        $this->source_url = $data['source_url'];
    }

    public function title()
    {
        return $this->title;
    }

    public function author()
    {
        return $this->author_id;
    }

    public function category()
    {
        return $this->category_id;
    }

    public function description()
    {
        return $this->description;
    }

    public function headImage()
    {
        return $this->head_image;
    }

    public function sourceURL()
    {
        return $this->source_url;
    }
}
