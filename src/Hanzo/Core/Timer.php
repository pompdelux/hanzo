<?php

namespace Hanzo\Core;

use Hanzo\Core\Tools;

class Timer
{
    protected $name = 'custom';
    protected $start;
    protected $laps = [];
    protected $last_lap = 0;

    public function __construct($name, $clean = false)
    {
        $this->name = $name;
        $this->start = $clean ? time() : $_SERVER['REQUEST_TIME_FLOAT'];
    }

    public function lap($label, $diff = true)
    {
        $lap = microtime(true) - $this->start;

        if ($diff) {
            $l = $lap - $this->last_lap;
        } else {
            $l = $lap;
        }

        $this->last_lap = $lap;

        $this->laps[$label] = $l;

        return $l;
    }

    public function getAll($as_string = false)
    {
        $this->laps['full trace'] = microtime(true) - $this->start;

        if ($as_string) {
            $string = '';
            foreach ($this->laps as $key => $value) {
                $string .= '- '.$key.': '.$value."\n";
            }

            return $string;
        }

        return $this->laps;
    }

    public function logOne($message, $threshold = 2)
    {
        $lap = $this->lap($message, true);
#        if ($lap > $threshold) {
            return $this->log($message." ".$lap);
#        }
    }

    public function logAll($message, $threshold = 2)
    {
#        if (array_sum($this->laps) > $threshold) {
            return $this->log($message."\n".$this->getAll(true));
#        }
    }

    public function reset($all = false)
    {
        if ($all) {
            $this->laps = [];
            $this->last_lap = 0;
            return;
        }

        $this->last_lap = microtime(true) - $this->start;
    }

    protected function log($message)
    {
        $bt = debug_backtrace();
        $line = $bt[1]['line'];
        $root = realpath(__DIR__ . '/../../../');
        $file = str_replace($root, '~', $bt[1]['file']);

        return error_log('['.date('r').'] '.$file.' +'.$line.' :: '.print_r($message, 1)."\n", 3, $root.'/app/logs/'.$this->name.'.log');
    }
}
