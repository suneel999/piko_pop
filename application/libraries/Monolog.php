<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Simple file logger used by admin orders and webhooks.
 */
class Monolog
{
    protected $log_path;

    public function __construct()
    {
        $this->log_path = APPPATH . 'logs' . DIRECTORY_SEPARATOR;
        if (!is_dir($this->log_path)) {
            @mkdir($this->log_path, 0755, true);
        }
    }

    public function info($message, $context = array(), $channel = 'app')
    {
        $this->write('INFO', $message, $context, $channel);
    }

    public function warning($message, $context = array(), $channel = 'app')
    {
        $this->write('WARNING', $message, $context, $channel);
    }

    public function error($message, $context = array(), $channel = 'app')
    {
        $this->write('ERROR', $message, $context, $channel);
    }

    protected function write($level, $message, $context, $channel)
    {
        $channel = preg_replace('/[^a-z0-9_-]/i', '', (string) $channel);
        if ($channel === '') {
            $channel = 'app';
        }

        $file = $this->log_path . $channel . '-' . date('Y-m-d') . '.log';
        $line = date('Y-m-d H:i:s') . ' [' . $level . '] ' . $message;

        if (!empty($context)) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        @file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
