<?php

namespace App\Utils\Log;

use Illuminate\Contracts\Support\Arrayable;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Monolog\Formatter\LineFormatter;
use Illuminate\Support\Facades\Log;

class FileDailyWriter implements PsrLoggerInterface
{

    const DEFAULT_NAME = 'filewriter';
    protected $levels = [
        'debug'     => Logger::DEBUG,
        'info'      => Logger::INFO,
        'notice'    => Logger::NOTICE,
        'warning'   => Logger::WARNING,
        'error'     => Logger::ERROR,
        'critical'  => Logger::CRITICAL,
        'alert'     => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    protected $fileLoggers = [];
    protected $filenameFormat;
    protected $dateFormat;
    protected $path;

    public function __construct($path = '') {
        $this->path = $path;
        $this->filenameFormat = '{filename}-{date}';
        $this->dateFormat = 'Y-m-d';
    }


    public function writeLog($level, $name, $message, array $context = [])
    {
        $filename = $this->getTimedFilename($name);
        if( !isset($this->fileLoggers[$filename]) ){
            Log::debug('new');
            $this->fileLoggers[$filename] = new Logger($name);
            $this->fileLoggers[$filename]->pushHandler(
                $handler = new StreamHandler(
                    storage_path() .'/logs/'. $filename . '.log',
                    $this->levels[$level]
                )
            );
            $handler->setFormatter($this->getDefaultFormatter());
        } else {
            Log::debug('findit');
        }

        return $this->fileLoggers[$filename]->{$level}($message, $context);
    }

    protected function getTimedFilename($name)
    {
        $timedFilename = str_replace(
            ['{filename}', '{date}'],
            [$name, date($this->dateFormat)],
            $this->filenameFormat
        );
        return $timedFilename;
    }

    //Log::name([option]'level', 'Message');
    function __call($func, $params){
        $level = 'info';
        $msg = $params[0];
        $lowerParams0 = strtolower($params[0]);
        if(in_array($lowerParams0, array_keys($this->levels))){
            $level = $lowerParams0;
            $msg = $params[1];
        }
        return $this->writeLog($level, $func, $this->formatMessage($msg));
    }

    /**
     * Format the parameters for the logger.
     *
     * @param  mixed  $message
     * @return mixed
     */
    protected function formatMessage($message)
    {
        if (is_array($message)) {
            return var_export($message, true);
        } elseif ($message instanceof Jsonable) {
            return $message->toJson();
        } elseif ($message instanceof Arrayable) {
            return var_export($message->toArray(), true);
        }

        return $message;
    }

    /**
     * Get a default Monolog formatter instance.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function getDefaultFormatter()
    {
        return tap(new LineFormatter(null, null, true, true), function ($formatter) {
            $formatter->includeStacktraces();
        });
    }

    public function emergency($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log an alert message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log a critical message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log an error message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log a notice to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log an informational message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log a debug message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, static::DEFAULT_NAME, $message, $context);
    }

    /**
     * Log a message to the logs.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog($level, static::DEFAULT_NAME, $message, $context);
    }
}