<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * SQLite doesn't support ALTER COLUMN, so we recreate the table
     * with the new enum values ('pausa', 'reanudacion' added).
     */
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        // Create new table with extended enum
        DB::statement("
            CREATE TABLE fichajes_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                tipo VARCHAR CHECK(tipo IN ('entrada','salida','pausa','reanudacion')) NOT NULL,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        // Copy existing data
        DB::statement('INSERT INTO fichajes_new SELECT * FROM fichajes');

        // Drop old table and rename
        Schema::drop('fichajes');
        DB::statement('ALTER TABLE fichajes_new RENAME TO fichajes');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement("
            CREATE TABLE fichajes_old (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                tipo VARCHAR CHECK(tipo IN ('entrada','salida')) NOT NULL,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        // Copy only entrada/salida records
        DB::statement("INSERT INTO fichajes_old SELECT * FROM fichajes WHERE tipo IN ('entrada','salida')");

        Schema::drop('fichajes');
        DB::statement('ALTER TABLE fichajes_old RENAME TO fichajes');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
