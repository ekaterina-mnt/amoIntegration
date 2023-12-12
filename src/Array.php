<?php

namespace App;

class ArrayPre 
{
    static function pre($array) 
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
}