<?php

namespace Logger;

use Loader\Config\ConfigLoader;

class Log
{
    public const DATE_FORMAT = 'm/d/Y h:i:s';

    /**
     * Error log file name
     *
     * @var null|string
     */
    private $error = null;

    private $levels = [
        'FATAL' => 1,
        'ERROR' => 2,
        'WARNING' => 3,
        'DEBUG' => 4,
        'INFO' => 5,
        'ALL' => 6
    ];

    /**
     * Directory name
     *
     * @var string
     */
    private $dir;

    private $level;

    private static $_instance;

    private $date_format;

    /**
     * Constructor
     *
     * @param string       $level  Levels
     * @param ConfigLoader $config Configuration
     */
    private function __construct($level = 'ALL', ?ConfigLoader $config = null)
    {
        $this->level = in_array($level, array_keys($this->levels))
            ? $level
            : 'ALL';
        $dir = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
        $default_config = [
            'logs' => $dir . '/logs',
            'date_format' => self::DATE_FORMAT,
        ];
        $configs = $config ? $config->getAll() : $default_config;
        $configs = array_merge($default_config, $configs);

        $this->dir = $configs['logs'] ?? '';
        $this->date_format = $configs['date_format'] ?? self::DATE_FORMAT;
        ! is_dir($this->dir) && @mkdir($this->dir, 0777);
        $this->_initialize();
    }

    /**
     * Initialize logs
     *
     * @return void
     */
    private function _initialize()
    {
        $dir = opendir($this->dir);
        while (($log = readdir($dir)) !== false) {
            if ($log == '.'
                || $log == '..'
                || !(str_ends_with($log, '-error.log'))
            ) {
                continue;
            }
            filectime($this->dir . '/' . $log) <= time() - 14 * 24 * 60 * 60
                && unlink($this->dir . '/' . $log);
        }
        closedir($dir);
        $prefix = Date('Y-m-d');
        $this->error = $this->error ?? $prefix . '-error.log';
    }

    /**
     * Disabling cloning the object from outside the class
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Returns the instance of Log
     *
     * @param string       $level  Default ALL
     * @param ConfigLoader $config Configuration
     *
     * @return Log
     */
    public static function getInstance($level = 'ALL', ?ConfigLoader $config = null): Log
    {
        self::$_instance = self::$_instance ?? new Log($level, $config);

        return self::$_instance;
    }

    /**
     * Add contents to Error log with ERROR Level
     *
     * @param string $msg  Error message
     * @param array  $data data to be append
     *
     * @return bool
     */
    public function error($msg, $data = null): bool
    {
        if ($this->levels[$this->level] >= $this->levels['ERROR']) {
            $msg = $this->format($msg, 'ERROR', date($this->date_format));

            return $this->_add($this->dir . '/' . $this->error, $msg, $data);
        }

        return true;
    }

    /**
     * Add contents to Error log with INFO Level
     *
     * @param string $msg  Error message
     * @param array  $data data to be append
     *
     * @return bool
     */
    public function info($msg, $data = null): bool
    {
        if ($this->levels[$this->level] >= $this->levels['INFO']) {
            $msg = $this->format($msg, 'INFO', date($this->date_format));

            return $this->_add($this->dir . '/' . $this->error, $msg, $data);
        }

        return true;
    }

    /**
     * Add contents to Error log with WARNING Level
     *
     * @param string $msg  Error message
     * @param array  $data data to be append
     *
     * @return bool
     */
    public function warning($msg, $data = null): bool
    {
        if ($this->levels[$this->level] >= $this->levels['WARNING']) {
            $msg = $this->format($msg, 'WARNING', date($this->date_format));

            return $this->_add($this->dir . '/' . $this->error, $msg, $data);
        }

        return true;
    }

    /**
     * Add contents to Error log with FATAL Level
     *
     * @param string $msg  Error message
     * @param array  $data data to be append
     *
     * @return bool
     */
    public function fatal($msg, $data = null): bool
    {
        if ($this->levels[$this->level] >= $this->levels['FATAL']) {
            $msg = $this->format($msg, 'FATAL', date($this->date_format));

            return $this->_add($this->dir . '/' . $this->error, $msg, $data);
        }

        return true;
    }

    /**
     * Add contents to Error log with DEBUG Level
     *
     * @param string $msg  Error message
     * @param array  $data data to be append
     *
     * @return bool
     */
    public function debug($msg, $data = null): bool
    {
        if ($this->levels[$this->level] >= $this->levels['DEBUG']) {
            $msg = $this->format($msg, 'DEBUG', date($this->date_format));

            return $this->_add($this->dir . '/' . $this->error, $msg, $data);
        }

        return true;
    }

    /**
     * Add Custom log message.
     *
     * @param string $file Filename
     * @param string $msg  Error message
     * @param array  $data data to be append
     *
     * @return bool
     */
    public function custom($file, $msg, $data = null): bool
    {
        $msg .= '[' . date($this->date_format) . ']';

        return $this->_add($this->dir . '/' . $file, $msg, $data);
    }

    /**
     * Format the log message
     *
     * @param string $msg   Message
     * @param string $level Level
     * @param string $date  Date
     *
     * @return string
     */
    protected function format(string $msg, string $level, string $date): string
    {
        return "[$date] [$level] : " . $msg;
    }

    /**
     * Writes to log file
     *
     * @param string     $file Filename
     * @param string     $msg  Message
     * @param null|array $data Data
     *
     * @return bool
     */
    private function _add(string $file, string $msg, ?array $data = null)
    {
        $data = $data ?? [];
        $data['context'] = [
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            // 'backtrace' => debug_backtrace()[2] ?? ''
        ];
        !file_exists($file)
            && fclose(fopen($file, 'w'));
        if (file_exists($file)) {
            ($data != null) && ($msg .= ', Data : ' . print_r($data, true));
            $msg .= "\n";
            file_put_contents($file, $msg, FILE_APPEND);

            return true;
        }

        return false;
    }
}
