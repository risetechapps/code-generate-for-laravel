<?php

namespace RiseTechApps\CodeGenerate\Contracts\Driver;

interface DatabaseDriverInterface
{
    public function getFieldType(string $table): array;
}
