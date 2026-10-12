<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->after('name');
        });

        $usedUsernames = [];

        DB::table('users')->orderBy('id')->select(['id', 'email'])->each(function (object $user) use (&$usedUsernames) {
            $base = Str::of($user->email ?? 'user')->before('@')->slug('.')->limit(50, '');

            if ($base->isEmpty()) {
                $base = Str::of('user');
            }

            $username = (string) $base;
            $suffix = 1;

            while (in_array($username, $usedUsernames, true)) {
                $suffix++;
                $username = Str::limit((string) $base, 50 - strlen((string) $suffix) - 1, '').'-'.$suffix;
            }

            $usedUsernames[] = $username;

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable(false)->change();
            $table->string('email')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
        });

        DB::table('users')->whereNull('email')->select(['id', 'username'])->eachById(function (object $user) {
            DB::table('users')->where('id', $user->id)->update([
                'email' => $user->username.'@jejakpas.invalid',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
};
