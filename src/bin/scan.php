<?php

/**
 * Scans a TeamSpeak 3 server for insecure HTTP links
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2018 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      https://composer.random-host.com
 */

namespace randomhost\TeamSpeak3;

use Exception;

// require autoload.php
$paths = array(
    __DIR__.'/../../../../autoload.php',
    __DIR__.'/../../../vendor/autoload.php',
    __DIR__.'/../../vendor/autoload.php',
);
foreach ($paths as $autoload) {
    if (file_exists($autoload)) {
        require $autoload;
        break;
    }
}
unset($paths, $autoload);

try {
    $check = new Scan();
    $check->setOptions(
        getopt(
            $check->getShortOptions(),
            $check->getLongOptions()
        )
    );
    $check->run();
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

