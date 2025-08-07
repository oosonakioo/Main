<?php

namespace Illuminate\Cache;

trait RetrievesMultipleKeys
{
    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @return array
     */
    public function many(array $keys)
    {
        $return = [];

        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }

        return $return;
    }

    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * @param  int  $minutes
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $minutes);
        }
    }
}
