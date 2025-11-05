<?php

namespace RiseTechApps\CodeGenerate;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RiseTechApps\CodeGenerate\Database\DatabaseDriverFactory;

class CodeGenerate
{
    public const length = 4;
    public const prefix = '';
    public const field = 'code';

    /**
     * @throws Exception
     */
    public static function generate($class): string
    {
        if ($class instanceof Model) {
            $model = $class;
        } elseif (is_string($class)) {
            $model = new $class();
            if (!$model instanceof Model) {
                throw new Exception('Provided class must extend ' . Model::class);
            }
        } else {
            throw new Exception('CodeGenerate::generate expects an Eloquent model instance or class name');
        }

        $table = $model->getTable();
        $connectionName = $model->getConnectionName();

        $driver = DatabaseDriverFactory::make($connectionName);
        $fieldInfo = $driver->getFieldType($table);
        $tableFieldType = $fieldInfo['type'];
        $tableFieldLength = $fieldInfo['length'];

        if (in_array($tableFieldType, ['int', 'integer', 'bigint', 'numeric']) && !is_numeric(self::prefix)) {
            throw new Exception(self::field . " field type is $tableFieldType but prefix is string");
        }

        if (self::length > $tableFieldLength) {
            throw new Exception('Generated ID length is bigger than table field length');
        }

        $prefixLength = strlen(self::prefix);
        $idLength = self::length - $prefixLength;

        return DB::connection($connectionName)->transaction(function () use ($table, $prefixLength, $idLength, $connectionName) {
            $latest = DB::connection($connectionName)
                ->table($table)
                ->select(self::field)
                ->orderBy(self::field, 'desc')
                ->lockForUpdate()
                ->first();

            if ($latest !== null && isset($latest->{self::field})) {
                $maxFullId = (string)$latest->{self::field};
                $maxId = substr($maxFullId, $prefixLength, $idLength);

                return self::prefix . str_pad((int)$maxId + 1, $idLength, '0', STR_PAD_LEFT);
            }

            return self::prefix . str_pad(1, $idLength, '0', STR_PAD_LEFT);
        });
    }
}
