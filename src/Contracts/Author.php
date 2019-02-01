<?php

namespace rogeecn\ArticleFetch\Contracts;


interface Author
{
    public function id();

    public function name();

    public function posts($count = 20);
}
