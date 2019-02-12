<?php

namespace rogeecn\ArticleFetch\Facades;


use Illuminate\Support\Facades\Facade;
use rogeecn\ArticleFetch\Classes\ArticleManager;
use rogeecn\ArticleFetch\Contracts\Author;
use rogeecn\ArticleFetch\Contracts\Content;
use rogeecn\ArticleFetch\Contracts\Summary;

/**
 * Class Article
 *
 * @method static full($id);
 * @method static int size($categoryID = null)
 * @method static Summary[] itemsAtPage($page = 1, $pageSize = 20, $categoryID = null)
 * @method static Summary[] random($size = 10, $categoryID = null)
 * @method static Summary summary($id)
 * @method static Content content($id)
 * @method static Author author($id)
 *
 * @package rogeecn\ArticleFetch\Facades
 */
class Article extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ArticleManager::class;
    }
}
