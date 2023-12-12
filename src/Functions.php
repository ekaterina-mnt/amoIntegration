<?php

namespace App;

use Inilim\JSON\JSON;

class Functions
{
    static $tmp = [];

    static function CollectDataException(\Throwable $e): array
    {
        $trace_str = $e->getTraceAsString();
        $trace = preg_split("#\n#", $trace_str);
        if ($trace === false) $trace = $trace_str;
        else $msg = $e->getMessage();
        return [
            'Msg'        => $msg,
            'Line'       => $e->getLine(),
            'Code'       => $e->getCode(),
            'File'       => $e->getFile(),
            'Trace'      => $trace,
            'Class_name' => get_class($e),
        ];
    }

    static function json():JSON
    {
        if(!isset(self::$tmp['json'])){
            self::$tmp['json'] = new JSON;
        }
        return self::$tmp['json'];
    }
}
