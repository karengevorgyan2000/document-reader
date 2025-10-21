<?php

namespace Kareng\DocumentReader;

use Illuminate\Support\ServiceProvider;

class DocumentReaderServiceProvider extends ServiceProvider
{
    public function register ()
    {
        $this->app->singleton(DocumentReaderService::class, function ($app) {
            return new DocumentReaderService();
        });
    }

    public function boot () {}
}
