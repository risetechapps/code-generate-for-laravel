<?php

namespace RiseTechApps\CodeGenerate;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class CodeGenerateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {

        if (!Blueprint::hasMacro('codeGenerate')) {
            Blueprint::macro('codeGenerate', function (?string $column = null, ?int $length = null, bool $unique = true) {
                /** @var Blueprint $this */
                $columnName = $column ?? CodeGenerate::field;
                $columnLength = $length ?? CodeGenerate::length;

                $definition = $this->string($columnName, $columnLength);

                if ($unique) {
                    $this->unique($columnName);
                }

                return $definition;
            });
        }
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
