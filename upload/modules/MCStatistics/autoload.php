<?php

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, [ROOT_PATH, 'modules', 'MCStatistics', 'classes', $class . '.php']);
    if (file_exists($path)) {
        require_once($path);
    }
});