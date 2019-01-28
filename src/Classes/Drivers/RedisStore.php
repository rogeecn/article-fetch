<?php

namespace rogeecn\ArticleFetch\Classes\Drivers;

use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Support\Facades\Storage;
use rogeecn\ArticleFetch\Contracts\Content;
use rogeecn\ArticleFetch\Contracts\Summary;
use Rogeecn\ArticleFetch\Exceptions\FetchContentDataFail;
use Rogeecn\ArticleFetch\Exceptions\FetchSummaryDataFail;

Class RedisStore implements \rogeecn\ArticleFetch\Contracts\Store
{
    /**
     * The Redis factory implementation.
     *
     * @var \Illuminate\Contracts\Redis\Factory
     */
    protected $redis;

    protected $prefix;

    /**
     * The Redis connection that should be used.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new Redis store.
     *
     * @param  \Illuminate\Contracts\Redis\Factory $redis
     * @param  string                              $prefix
     * @param  string                              $connection
     *
     * @return void
     */
    public function __construct(Redis $redis, $prefix, $connection = 'default')
    {
        $this->redis = $redis;
        $this->setPrefix($prefix);
        $this->setConnection($connection);
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = !empty($prefix) ? $prefix . ':' : '';
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function connection()
    {
        return $this->redis->connection($this->connection);
    }


    public function itemsAtPage($page = 1, $pageSize = 20, $categoryID = null)
    {
        $startPosition = ($page - 1) * $pageSize;
        $keys = $this->connection()->lrange(
            $this->getKey($this->prefix, $categoryID),
            $startPosition,
            $pageSize + $startPosition
        );

        return collect($keys)->map(function ($key) {
            try {
                $this->summary($key);
            } catch (\Exception $e) {
                report($e);
                return null;
            }
        })->filter();
    }

    public function full($id)
    {
        return collect([
            'summary' => $this->summary($id),
            'content' => $this->content($id),
        ]);
    }

    public function summary($id): Summary
    {
        $summaryStoreKey = config('article.redis.store.summary');
        if ($this->connection()->exists($summaryStoreKey)) {
            $summaryData = $this->connection()->hget($summaryStoreKey, $id);
        } else {
            $filePath = "summary/{$id}.json";
            $summaryData = Storage::cloud()->get($filePath);
        }
        $summaryData = json_decode($summaryData, true);
        if (!$summaryData) {
            throw new FetchSummaryDataFail();
        }

        return new \rogeecn\ArticleFetch\Classes\article\Summary($summaryData);
    }

    public function content($id): Content
    {

        $filePath = "content/{$id}.json";
        $contentData = Storage::cloud()->get($filePath);
        $contentData = json_decode($contentData, true);
        if (!$contentData) {
            throw new FetchContentDataFail();
        }

        return new \rogeecn\ArticleFetch\Classes\article\Content($contentData);
    }

    private function getKey($prefix, $categoryID = null)
    {
        $key = $this->prefix . "_";
        if (!is_null($categoryID)) {
            $key = $prefix;
        }
        return $key;
    }
}
