<?php

if (!function_exists('z_debug')) {
    function z_debug($var)
    {
        echo '<div style="direction: ltr !important; text-align: left !important;"><pre>';
        print_r($var);
        echo '</pre></div>';
        exit();
    }
}