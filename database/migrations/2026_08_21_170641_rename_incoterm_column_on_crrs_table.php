<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crrs')) {
            return;
        }

        $column = $this->incotermColumnName();
        if ($column === null || $column === 'incoterm') {
            return;
        }

        // MySQL keeps the created casing in result sets; Eloquent then misses lowercase `incoterm`.
        DB::statement('ALTER TABLE `crrs` CHANGE `'.$column.'` `incoterm` VARCHAR(255) NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('crrs')) {
            return;
        }

        if ($this->incotermColumnName() !== 'incoterm') {
            return;
        }

        DB::statement('ALTER TABLE `crrs` CHANGE `incoterm` `Incoterm` VARCHAR(255) NULL');
    }

    private function incotermColumnName(): ?string
    {
        foreach (DB::select('SHOW COLUMNS FROM `crrs`') as $column) {
            if (strcasecmp((string) $column->Field, 'incoterm') === 0) {
                return (string) $column->Field;
            }
        }

        return null;
    }
};
