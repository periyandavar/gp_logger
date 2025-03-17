<?php

namespace Logger;

use Loader\Config\ConfigLoader;
use PSpell\Config;

class Log
{
    /**
     * Activity log file name
     *
     * @var null|string
     */
    private $_activity = null;

    /**
     * Error log file name
     *
     * @var null|string
     */
    private $_error = null;

    private $_levels = [
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
    private $_dir;

    private $_level;

    private static $_instance;

    /**
     * Undocumented function
     *
     * @param string $level Levels
     * @param ConfigLoader $config Configuration
     */
    private function __construct($level = 'ALL', ?ConfigLoader $config = null)
    {
        $this->_level = in_array($level, array_keys($this->_levels))
            ? $level
            : "ALL";
        $default_config = [
            'logs' => __DIR__ . '../../../logs'
        ];
        $configs = $config ? $config->getAll() : $default_config;
        $configs = array_merge($default_config, $configs);

        $this->_dir = $config['logs'];
        !is_dir($this->_dir) and mkdir($this->_dir, 0777);
        $this->_initialize();
    }

    /**
     * Initialize logs
     *
     * @return void
     */
    private function _initialize()
    {
        $dir = opendir($this->_dir);
        while (($log = readdir($dir)) !== false) {
            if ($log == '.'
                || $log == '..'
                || !(str_ends_with($log, '-error.log'))
            ) {
                continue;
            }
            filectime($this->_dir . '/' . $log) <= time() - 14 * 24 * 60 * 60
                and unlink($this->_dir . '/' . $log);
        }
        closedir($dir);
        $prefix = Date('Y-m-d');
        $this->_error = $this->_error ?? $prefix . '-error.log';
        $this->_activity = $this->_activity ?? $prefix . '-activity.log';
        !file_exists($this->_dir .'/'. $this->_error)
            and fclose(fopen($this->_dir .'/'. $this->_error, 'w'));
        !file_exists($this->_dir .'/'. $this->_activity)
            and fclose(fopen($this->_dir .'/'. $this->_activity, 'w'));
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
     * @param string $level Default ALL
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
     * @param array $data data to be append
     *
     * @return bool
     */
    public function error($msg, $data = null): bool
    {
        if ($this->_levels[$this->_level] >= $this->_levels['ERROR']) {
            $msg = $this->format($msg, 'ERROR', date("m/d/Y h:i:s"));
            return $this->_add($this->_dir . '/' . $this->_error, $msg, $data);
        }
        return true;
    }

    /**
     * Add contents to Error log with INFO Level
     *
     * @param string $msg  Error message
     * @param array $data data to be append
     *
     * @return bool
     */
    public function info($msg, $data = null): bool
    {
        if ($this->_levels[$this->_level] >= $this->_levels['INFO']) {
            $msg = $this->format($msg, 'INFO', date("m/d/Y h:i:s"));
            return $this->_add($this->_dir . '/' . $this->_error, $msg, $data);
        }
        return true;
    }

    /**
     * Add contents to Error log with WARNING Level
     *
     * @param string $msg  Error message
     * @param array $data data to be append
     *
     * @return bool
     */
    public function warning($msg, $data = null): bool
    {
        if ($this->_levels[$this->_level] >= $this->_levels['WARNING']) {
            $msg = $this->format($msg, 'WARNING', date("m/d/Y h:i:s"));
            return $this->_add($this->_dir . '/' . $this->_error, $msg, $data);
        }
        return true;
    }

    /**
     * Add contents to Error log with FATAL Level
     *
     * @param string $msg  Error message
     * @param array $data data to be append
     *
     * @return bool
     */
    public function fatal($msg, $data = null): bool
    {
        if ($this->_levels[$this->_level] >= $this->_levels['FATAL']) {
            $msg = $this->format($msg, 'FATAL', date("m/d/Y h:i:s"));
            return $this->_add($this->_dir . '/' . $this->_error, $msg, $data);
        }
        return true;
    }

    /**
     * Add contents to Error log with DEBUG Level
     *
     * @param string $msg  Error message
     * @param array $data data to be append
     *
     * @return bool
     */
    public function debug($msg, $data = null): bool
    {
        if ($this->_levels[$this->_level] >= $this->_levels['DEBUG']) {
            $msg = $this->format($msg, 'DEBUG', date("m/d/Y h:i:s"));
            return $this->_add($this->_dir . '/' . $this->_error, $msg, $data);
        }
        return true;
    }

    /**
     * Add contents to activity log
     *
     * @param string $msg  Error message
     * @param array $data data to be append
     *
     * @return bool
     */
    public function activity($msg, $data = null): bool
    {
        $msg = $this->format($msg, 'INFO', date("m/d/Y h:i:s"));
        return $this->_add($this->_dir . '/' . $this->_activity, $msg, $data);
    }

    /**
     * Add contents to activity log
     *
     * @param string $file Filename
     * @param string $msg  Error message
     * @param array $data data to be append
     *
     * @return bool
     */
    public function custom($file, $msg, $data = null): bool
    {
        $msg .= "[" . date("m/d/Y h:i:s") . "]";
        return $this->_add($this->_dir . '/' . $file, $msg, $data);
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
        $format = "[$date] [$level] : " . $msg;
        return $format;
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
        if (file_exists($file)) {
            ($data != null) and ($msg .= ", Data : " . print_r($data, true));
            $msg .= "\n";
            file_put_contents($file, $msg, FILE_APPEND);
            return true;
        }
        return false;
    }
}
