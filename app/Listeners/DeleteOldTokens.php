<?php

namespace App\Listeners;

use Laravel\Passport\Token;
use Laravel\Passport\Events\AccessTokenCreated;

class DeleteOldTokens
{
    /**
     * Handle the event.
     *
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        Token::where([
            ['user_id', $event->userId],
            ['id', '<>', $event->tokenId],
        ])->delete();
    }
}
