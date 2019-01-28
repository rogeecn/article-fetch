<?php

namespace rogeecn\ArticleFetch\Classes;

use Closure;
use InvalidArgumentException;
use rogeecn\ArticleFetch\Contracts\Content;
use rogeecn\ArticleFetch\Contracts\Store;
use rogeecn\ArticleFetch\Contracts\Summary;


/**
 * Class ArticleManager
 *
 *
 * @method full($id);
 * @method Summary[] itemsAtPage($page = 1, $pageSize = 20, $categoryID = null)
 * @method Summary summary($id)
 * @method Content content($id)
 *
 *
 * @package rogeecn\ArticleFetch\Classes
 */
class ArticleManager
{
    private   $app;
    private   $stores;
    protected $customCreators = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function repository(Store $store)
    {
        $repository = new Repository($store);
        return $repository;
    }

    public function __call($method, $parameters)
    {
        return $this->store()->$method(...$parameters);
    }

    public function store($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->stores[$name] = $this->get($name);
    }

    public function getDefaultDriver()
    {
        return $this->app['config']['article.default'];
    }

    public function setDefaultDriver($name)
    {
        $this->app['config']['article.default'] = $name;
    }

    protected function get($name)
    {
        return $this->stores[$name] ?? $this->resolve($name);
    }

    protected function createDatabaseDriver()
    {
        $connection = $this->app['db']->connection($config['connection'] ?? null);

        return $this->repository(new \rogeecn\ArticleFetch\Classes\Drivers\DatabaseStore($connection, $config['table']));
    }

    protected function createRedisDriver(array $config)
    {
        $redis = $this->app['redis'];
        $prefix = config('article.drivers.redis.prefix');
        $connection = $config['connection'] ?? 'default';

        return $this->repository(new \rogeecn\ArticleFetch\Classes\Drivers\RedisStore($redis, $prefix, $connection));
    }

    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Article store [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

            if (method_exists($this, $driverMethod)) {
                return $this->{$driverMethod}($config);
            } else {
                throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
            }
        }
    }

    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }


    protected function getConfig($name)
    {
        return $this->app['config']["article.drivers.{$name}"];
    }

    public function driver($driver = null)
    {
        return $this->store($driver);
    }

    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }
}
