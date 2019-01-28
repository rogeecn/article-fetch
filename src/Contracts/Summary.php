<?php

namespace rogeecn\ArticleFetch\Contracts;


interface Summary
{
    public function title();

    public function author();

    public function category();

    public function description();

    public function headImage();

    public function sourceURL();
}
