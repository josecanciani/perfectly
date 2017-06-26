<?php

namespace Perfectly;

spl_autoload_register(function ($className) {
    if (substr($className, 0, 9) == __namespace__) {
        require dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($className, 10)) . '.php';
    }
});
