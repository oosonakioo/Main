<?php

namespace League\Flysystem;

final class SafeStorage
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var array
     */
    protected static $safeStorage = [];

    public function __construct()
    {
        $this->hash = spl_object_hash($this);
        self::$safeStorage[$this->hash] = [];
    }

    public function storeSafely($key, $value)
    {
        self::$safeStorage[$this->hash][$key] = $value;
    }

    public function retrieveSafely($key)
    {
        if (array_key_exists($key, self::$safeStorage[$this->hash])) {
            return self::$safeStorage[$this->hash][$key];
        }
    }

    public function __destruct()
    {
        unset(self::$safeStorage[$this->hash]);
    }
}
