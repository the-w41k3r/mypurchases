<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mypurchases_settings')) {
            Schema::create('mypurchases_settings', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        $existingSettings = DB::table('mypurchases_settings')
                            ->whereIn('name', ['tebex_secret', 'tebex_store_url'])
                            ->pluck('name')
                            ->toArray();

        if (!in_array('tebex_secret', $existingSettings)) {
            DB::table('mypurchases_settings')->insert([
                'name' => 'tebex_secret',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        if (!in_array('tebex_store_url', $existingSettings)) {
            DB::table('mypurchases_settings')->insert([
                'name' => 'tebex_store_url',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('mypurchases_settings');
    }
};
