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
        $table = '';

        if ($class instanceof Model) {
            $table = $class->getTable();
        }

        if (gettype($class) === "string") {
            $class = new $class();
            $table = $class->getTable();
        }

        $driver = DatabaseDriverFactory::make();
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

        $totalQuery = sprintf("SELECT count(%s) total FROM %s", self::field, $table);
        $total = DB::connection($class->getConnectionName())->select(trim($totalQuery));

        if ($total[0]->total) {
            $maxQuery = sprintf("SELECT MAX(%s) AS maxid FROM %s", self::field, $table);
            $queryResult = DB::connection($class->getConnectionName())->select($maxQuery);

            $maxFullId = $queryResult[0]->maxid;
            $maxId = substr($maxFullId, $prefixLength, $idLength);

            return self::prefix . str_pad((int)$maxId + 1, $idLength, '0', STR_PAD_LEFT);
        } else {
            return self::prefix . str_pad(1, $idLength, '0', STR_PAD_LEFT);
        }
    }
}
