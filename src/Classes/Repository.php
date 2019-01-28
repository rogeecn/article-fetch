<?php

namespace rogeecn\ArticleFetch\Classes;

use Illuminate\Support\Traits\Macroable;
use rogeecn\ArticleFetch\Contracts\Store;

class Repository
{
    use Macroable {
        __call as macroCall;
    }

    protected $store;


    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->store->$method(...$parameters);
    }
}
