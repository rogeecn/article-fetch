<?php

namespace rogeecn\ArticleFetch\Contracts;

interface Store
{
    public function itemsAtPage($page = 1, $pageSize = 20, $categoryID = null);

    public function random($size = 10, $categoryID = null);

    public function size($categoryID = null);

    public function full($id);

    public function summary($id): Summary;

    public function content($id): Content;
}
