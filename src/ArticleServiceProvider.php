<?php

namespace rogeecn\ArticleFetch;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use rogeecn\ArticleFetch\Classes\ArticleManager;

class ArticleServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $this->publishes([__DIR__ . '/Config/article.php' => config_path('article.php'),], 'article');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/article.php', 'article');

        $this->app->singleton('article', function () {
            return new ArticleManager($this->app);
        });
    }
}
