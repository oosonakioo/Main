<?php

namespace Maatwebsite\Excel\Files;

use Illuminate\Foundation\Application;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;

abstract class File
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Excel instance
     *
     * @var Excel
     */
    protected $excel;

    /**
     * Loaded file
     *
     * @var \Maatwebsite\Excel\Readers\LaravelExcelReader
     */
    protected $file;

    public function __construct(Application $app, Excel $excel)
    {
        $this->app = $app;
        $this->excel = $excel;
    }

    /**
     * Handle the import/export of the file
     *
     * @return mixed
     *
     * @throws LaravelExcelException
     */
    public function handle($type)
    {
        // Get the handler
        $handler = $this->getHandler($type);

        // Call the handle method and inject the file
        return $handler->handle($this);
    }

    /**
     * Get handler
     *
     * @return mixed
     *
     * @throws LaravelExcelException
     */
    protected function getHandler($type)
    {
        return $this->app->make(
            $this->getHandlerClassName($type)
        );
    }

    /**
     * Get the file instance
     *
     * @return mixed
     */
    public function getFileInstance()
    {
        return $this->file;
    }

    /**
     * Get the handler class name
     *
     * @return string
     *
     * @throws LaravelExcelException
     */
    protected function getHandlerClassName($type)
    {
        // Translate the file into a FileHandler
        $class = get_class($this);
        $handler = substr_replace($class, $type.'Handler', strrpos($class, $type));

        // Check if the handler exists
        if (! class_exists($handler)) {
            throw new LaravelExcelException("$type handler [$handler] does not exist.");
        }

        return $handler;
    }
}
