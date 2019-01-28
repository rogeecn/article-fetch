<?php

namespace rogeecn\ArticleFetch\Classes\Drivers;

use Illuminate\Database\ConnectionInterface;
use rogeecn\ArticleFetch\Contracts\Content;
use rogeecn\ArticleFetch\Contracts\Summary;

Class DatabaseStore implements \rogeecn\ArticleFetch\Contracts\Store
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The name of the cache table.
     *
     * @var string
     */
    protected $table;

    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new database store.
     *
     * @param  \Illuminate\Database\ConnectionInterface $connection
     * @param  string                                   $table
     * @param  string                                   $prefix
     *
     * @return void
     */
    public function __construct(ConnectionInterface $connection, $table, $prefix = '')
    {
        $this->table = $table;
        $this->prefix = $prefix;
        $this->connection = $connection;
    }

    public function itemsAtPage($page = 1, $pageSize = 20, $categoryID = null)
    {

    }

    public function full($id)
    {
        return [
            'summary' => $this->summary($id),
            'content' => $this->content($id),
        ];
    }

    public function summary($id): Summary
    {
        return new \rogeecn\ArticleFetch\Classes\article\Summary($id);
    }

    public function content($id): Content
    {
        return new \rogeecn\ArticleFetch\Classes\article\Content($id);
    }
}
