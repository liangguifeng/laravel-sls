<?php

namespace Seffeng\LaravelSLS;

use Psr\Log\LoggerInterface;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;

class Writer implements LoggerInterface
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var SLSLog
     */
    private $logger;

    /**
     * @var string
     */
    private $env;

    /**
     * @author zxf
     * @date   2020年11月24日
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(SLSLog $logger, Dispatcher $dispatcher = null, string $env = '')
    {
        if (isset($dispatcher)) {
            $this->dispatcher = $dispatcher;
        }

        $this->logger = $logger;
        $this->env    = $env;
    }

    /**
     * Log an alert message to the logs.
     *
     * @param string $message
     */
    public function alert($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a critical message to the logs.
     *
     * @param string $message
     */
    public function critical($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an error message to the logs.
     *
     * @param string $message
     */
    public function error($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param string $message
     */
    public function warning($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a notice to the logs.
     *
     * @param string $message
     */
    public function notice($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an informational message to the logs.
     *
     * @param string $message
     */
    public function info($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a debug message to the logs.
     *
     * @param string $message
     */
    public function debug($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a message to the logs.
     *
     * @param string $level
     * @param string $message
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Register a file log handler.
     *
     * @param string $path
     * @param string $level
     */
    public function useFiles($path, $level = 'debug')
    {
    }

    /**
     * Register a daily file log handler.
     *
     * @param string $path
     * @param int    $days
     * @param string $level
     */
    public function useDailyFiles($path, $days = 0, $level = 'debug')
    {
    }

    /**
     * System is unusable.
     *
     * @param string $message
     */
    public function emergency($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Write a message to Monolog.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    protected function writeLog($level, $message, $context)
    {
        $this->fireLogEvent($level, $message = $this->formatMessage($message), $context);

        $this->logger->putLogs([
            'level'   => $level,
            'env'     => $this->env,
            'message' => $message,
            'context' => json_encode($context),
        ]);
    }

    /**
     * Fires a log event.
     *
     * @param string $level
     * @param string $message
     */
    protected function fireLogEvent($level, $message, array $context = [])
    {
        // If the event dispatcher is set, we will pass along the parameters to the
        // log listeners. These are useful for building profilers or other tools
        // that aggregate all of the log messages for a given "request" cycle.
        if (isset($this->dispatcher)) {
            $this->dispatcher->dispatch(new MessageLogged($level, $message, $context));
        }
    }

    /**
     * Format the parameters for the logger.
     *
     * @param mixed $message
     *
     * @return mixed
     */
    protected function formatMessage($message)
    {
        if (is_array($message)) {
            return var_export($message, true);
        }

        if ($message instanceof Jsonable) {
            return $message->toJson();
        }

        if ($message instanceof Arrayable) {
            return var_export($message->toArray(), true);
        }

        return $message;
    }
}
