<?php

namespace App\Listeners;

class DeleteOldRefreshTokens
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        \DB::table('oauth_refresh_tokens')
            ->whereDate('expires_at', '<', now()->addDays(1))
            ->delete();
    }
}
