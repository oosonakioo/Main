<?php

namespace Maatwebsite\Excel\Files;

interface ImportHandler
{
    /**
     * Handle the import
     *
     * @return mixed
     */
    public function handle($file);
}
