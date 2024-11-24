<?php

namespace RiseTechApps\CodeGenerate\Database\Driver;

use Exception;
use Illuminate\Support\Facades\DB;
use RiseTechApps\CodeGenerate\CodeGenerate;
use RiseTechApps\CodeGenerate\Contracts\Driver\DatabaseDriverInterface;

class MysqlDatabase implements DatabaseDriverInterface
{

    /**
     * @throws Exception
     */
    public function getFieldType(string $table): array
    {
        $database = DB::connection()->getDatabaseName();
        $sql = 'SELECT column_name AS "column_name", data_type AS "data_type", column_type AS "column_type" FROM information_schema.columns ';
        $sql .= 'WHERE table_schema = :database AND table_name = :table';

        $rows = DB::select($sql, ['database' => $database, 'table' => $table]);

        $fieldType = null;
        $fieldLength = 20;

        foreach ($rows as $col) {
            if (CodeGenerate::field == $col->column_name) {
                $fieldType = $col->data_type;

                preg_match("/(?<=\().+?(?=\))/", $col->column_type, $tblFieldLength);
                if (count($tblFieldLength)) {
                    $fieldLength = $tblFieldLength[0];
                }
                break;
            }
        }

        if ($fieldType == null) {
            throw new Exception(CodeGenerate::field . " not found in $table table");
        }

        return ['type' => $fieldType, 'length' => $fieldLength];
    }
}
