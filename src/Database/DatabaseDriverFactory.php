<?php

namespace RiseTechApps\CodeGenerate\Database;

use Exception;
use Illuminate\Support\Facades\DB;
use RiseTechApps\CodeGenerate\Contracts\Driver\DatabaseDriverInterface;

class DatabaseDriverFactory
{
    /**
     * @throws Exception
     */
    public static function make(): DatabaseDriverInterface
    {
        $connection = config('database.default');
        $driver = DB::connection($connection)->getDriverName();

        return match ($driver) {
            'mysql' => new Driver\MysqlDatabase(),
            'pgsql' => new Driver\PostgreSQLDatabase(),
            'sqlsrv' => new Driver\SQLServerDatabase(),
            default => throw new Exception("Unsupported database driver: $driver"),
        };
    }
}
