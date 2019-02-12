<?php

namespace rogeecn\ArticleFetch;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use rogeecn\ArticleFetch\Classes\ArticleManager;
use rogeecn\ArticleFetch\Contracts\Store;

class ArticleServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $this->publishes([__DIR__ . '/Config/article.php' => config_path('article/article.php'),], 'article');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/article.php', 'article');

        $this->app->singleton(ArticleManager::class, function () {
            return new ArticleManager($this->app);
        });
    }
}
