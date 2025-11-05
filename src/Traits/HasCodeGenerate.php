<?php

namespace RiseTechApps\CodeGenerate\Traits;

use RiseTechApps\CodeGenerate\CodeGenerate;
use Illuminate\Support\Facades\Schema;


trait HasCodeGenerate
{
    protected static bool $ignoreCodeGenerateUpdating = false;

    protected static function bootHasCodeGenerate(): void
    {
        static::creating(/**
         * @throws \Exception
         */ function ($model) {
            $connection = $model->getConnectionName();
            $schemaBuilder = Schema::connection($connection ?? config('database.default'));

            if ($schemaBuilder->hasTable($model->getTable())) {
                $model->code = CodeGenerate::generate($model);
            }

        });

        static::updating(function ($model) {
            if (!self::$ignoreCodeGenerateUpdating) {
                $model->code = $model->getOriginal('code');
            }
        });
    }
}
