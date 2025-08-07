<?php

namespace Illuminate\Contracts\Debug;

use Exception;

interface ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report(Exception $e);

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e);

    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function renderForConsole($output, Exception $e);
}
