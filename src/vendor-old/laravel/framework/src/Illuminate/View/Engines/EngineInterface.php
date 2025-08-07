<?php

namespace Illuminate\View\Engines;

interface EngineInterface
{
    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @return string
     */
    public function get($path, array $data = []);
}
