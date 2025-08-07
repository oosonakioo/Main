<?php

namespace Illuminate\Contracts\Redis;

interface Database
{
    /**
     * Run a command against the Redis database.
     *
     * @param  string  $method
     * @return mixed
     */
    public function command($method, array $parameters = []);
}
