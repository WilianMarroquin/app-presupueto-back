<?php

function disableForeignKeys(): void
{
    if (config('database.default') == 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }
}

function enableForeignKeys(): void
{
    if (config('database.default') == 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
