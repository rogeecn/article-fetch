<?php

namespace rogeecn\ArticleFetch\Classes\article;


use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;

class Summary implements \rogeecn\ArticleFetch\Contracts\Summary
{
    private $id;
    private $author_id;
    private $category_id;
    private $title;
    private $description;
    private $head_image;
    private $source_url;
    private $created_at;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->author_id = $data['author_id'];
        $this->category_id = $data['category_id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->head_image = $data['head_image'];
        $this->source_url = $data['source_url'];
        $this->created_at = $data['created_at'];
    }

    public function id()
    {
        return $this->id;
    }

    public function hashID()
    {
        return Hashids::encode($this->id());
    }

    public function title()
    {
        return html_entity_decode($this->title);
    }

    public function author(): \rogeecn\ArticleFetch\Contracts\Author
    {
        return new Author($this->author_id);
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

    public function publishAt()
    {
        return Carbon::createFromTimestamp($this->created_at)->format("y/m/d");
    }
}
