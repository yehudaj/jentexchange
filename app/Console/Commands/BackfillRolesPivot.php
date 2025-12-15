<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackfillRolesPivot extends Command
{
    protected $signature = 'roles:backfill';
    protected $description = 'Backfill role_user pivot from users.roles JSON column';

    public function handle(): int
    {
        $this->info('Starting backfill...');
        \DB::table('users')->whereNotNull('roles')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $u) {
                $roles = json_decode($u->roles, true) ?: [];
                foreach ($roles as $r) {
                    $role = \App\Models\Role::firstOrCreate(['name' => strtoupper($r)]);
                    if (! \DB::table('role_user')->where('user_id', $u->id)->where('role_id', $role->id)->exists()) {
                        \DB::table('role_user')->insert(['user_id' => $u->id, 'role_id' => $role->id]);
                    }
                }
            }
        });
        $this->info('Backfill complete.');
        return 0;
    }
}
