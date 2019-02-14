<?php

namespace rogeecn\ArticleFetch\Classes\Drivers;

use App\CacheKey;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Support\Facades\Storage;
use rogeecn\ArticleConf\Classes\CategoryItem;
use rogeecn\ArticleFetch\Contracts\Author;
use rogeecn\ArticleFetch\Contracts\Content;
use rogeecn\ArticleFetch\Contracts\Summary;
use rogeecn\ArticleFetch\Exceptions\FetchContentDataFail;
use rogeecn\ArticleFetch\Exceptions\FetchSummaryDataFail;

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
        $this->prefix = !empty($prefix) ? $prefix : '';
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function connection()
    {
        return $this->redis->connection($this->connection);
    }

    public function random($size = 5, $categoryID = null)
    {
        $listSize = $this->size($categoryID);
        if ($listSize <= 0) {
            return [];
        }

        $keys = [];
        for ($i = 0; $i < $size; $i++) {
            $start = mt_rand(0, $listSize - $size);
            $keys[] = $this->connection()->lindex($this->getKey($this->prefix, $categoryID), $start);
        }

        return $this->getItemsFromKeys($keys);
    }


    public function size($categoryID = null)
    {
        return $this->connection()->llen($this->getKey($this->prefix, $categoryID));
    }

    public function itemsAtPage($page = 1, $pageSize = 20, CategoryItem $category = null)
    {
        $startPosition = ($page - 1) * $pageSize;
        $keys = $this->connection()->lrange(
            $this->getKey($this->prefix, $category ? $category->id : null),
            $startPosition,
            $pageSize + $startPosition - 1
        );

        return $this->getItemsFromKeys($keys);
    }

    private function getItemsFromKeys($keys)
    {
        return collect($keys)->map(function ($key) {
            try {
                return $this->summary($key);
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

    public function author($id): Author
    {
        $cacheKey = config('article.drivers.redis.store.author');

        $data = "";
        if ($isExists = $this->connection()->hexists($cacheKey, $id)) {
            $data = $this->connection()->hget($cacheKey, $id);
        }

        return new \rogeecn\ArticleFetch\Classes\article\Author($id, $data);
    }

    public function summary($id): Summary
    {
        $summaryStoreKey = config('article.drivers.redis.store.summary');
        if ($isExists = $this->connection()->hexists($summaryStoreKey, $id)) {
            $summaryData = $this->connection()->hget($summaryStoreKey, $id);
        } else {
            $filePath = "summary/{$id}.json";
            $summaryData = Storage::cloud()->get($filePath);

            if (env("APP_ENV") == 'local') {
                $this->connection()->hset($summaryStoreKey, $id, $summaryData);
            }
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
        $key = CacheKey::SITE_RECENT_LIST . $prefix;
        if (!is_null($categoryID)) {
            $key .= ":{$categoryID}";
        }
        return $key;
    }
}
