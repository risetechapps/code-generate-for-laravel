<?php

namespace RiseTechApps\CodeGenerate;

use Illuminate\Support\ServiceProvider;

class CodeGenerateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(CodeGenerate::class, function () {
            return new CodeGenerate();
        });

        $this->app->alias(CodeGenerate::class, 'code-generate');
    }
}
