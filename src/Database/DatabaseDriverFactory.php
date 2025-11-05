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
    public static function make(?string $connectionName = null): DatabaseDriverInterface
    {
        $driver = DB::connection($connectionName)->getDriverName();

        return match ($driver) {
            'mysql' => new Driver\MysqlDatabase($connectionName),
            'pgsql' => new Driver\PostgreSQLDatabase($connectionName),
            'sqlsrv' => new Driver\SQLServerDatabase($connectionName),
            default => throw new Exception("Unsupported database driver: $driver"),
        };
    }
}
