<?php

namespace Maatwebsite\Excel\Files;

interface ExportHandler
{
    /**
     * Handle the export
     *
     * @return mixed
     */
    public function handle($file);
}
