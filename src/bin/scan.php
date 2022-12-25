<?php

/**
 * Scans a TeamSpeak 3 server for insecure HTTP links.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */

namespace randomhost\TeamSpeak3;

// require autoload.php
$paths = [
    __DIR__.'/../../../../autoload.php',
    __DIR__.'/../../../vendor/autoload.php',
    __DIR__.'/../../vendor/autoload.php',
];
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
} catch (\Exception $e) {
    echo $e->getMessage();

    exit(1);
}
