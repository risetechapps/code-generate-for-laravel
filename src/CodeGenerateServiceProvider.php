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
        // Register the main class to use with the facade
        $this->app->singleton('code-generate', function () {
            return new CodeGenerate;
        });

        $this->app->singleton(CodeGenerate::class, function () {
            return new CodeGenerate;
        });

        $this->app->singleton(CodeGenerate::class);
    }
}
