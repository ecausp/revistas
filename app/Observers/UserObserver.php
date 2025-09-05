<?php
namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public function created(User $user): void
    {
        $admins = collect(explode(',', env('SENHAUNICA_ADMINS', '')))
            ->map(fn($v) => trim($v))
            ->filter()
            ->map(fn($v) => (string)$v) // normaliza como string
            ->all();

        if (in_array((string)$user->codpes, $admins, true)) {
            // garante que a role exista no guard correto
            $guard = property_exists($user, 'guard_name') ? $user->guard_name : config('auth.defaults.guard', 'web');
            Role::findOrCreate('admin', $guard);

            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        }
    }
}
