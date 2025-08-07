<?php

namespace Illuminate\Contracts\Broadcasting;

interface Broadcaster
{
    /**
     * Broadcast the given event.
     *
     * @param  string  $event
     * @return void
     */
    public function broadcast(array $channels, $event, array $payload = []);
}
