<?php

namespace rogeecn\ArticleFetch\Classes\article;


class Content implements \rogeecn\ArticleFetch\Contracts\Content
{
    private $mode;
    private $content;

    public function __construct($data)
    {
        $this->mode = $data['mode'];
        $this->content = $data['content'];
    }

    public function mode()
    {
        return $this->mode;
    }

    public function content()
    {
//        return $this->content;
//
//        echo "\n\n\n----------------\n\n\n";

        return \rogeecn\ArticleConf\Facads\Content::replaceImage($this->content);
        exit;
    }
}
