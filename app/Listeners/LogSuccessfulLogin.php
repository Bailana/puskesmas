<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\ActivityLog;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        ActivityLog::create([
            'user_id' => $event->user->id,
            'action' => 'Login',
            'description' => 'Pengguna Login',
        ]);
    }
}
