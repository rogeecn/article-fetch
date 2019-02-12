<?php

namespace rogeecn\ArticleFetch\Contracts;


interface Summary
{
    public function id();

    public function hashID();

    public function title();

    public function author(): Author;

    public function publishAt();

    public function category();

    public function description();

    public function headImage();

    public function sourceURL();
}
