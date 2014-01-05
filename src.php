<?php

include __DIR__ . '/src/Mindy/Helper/Dumper.php';

include __DIR__ . '/src/Mindy/Logger/Logger.php';
include __DIR__ . '/src/Mindy/Logger/Target/Target.php';
include __DIR__ . '/src/Mindy/Logger/Target/DummyTarget.php';

use Mindy\Helper\Dumper;

function d() {
    $debug = debug_backtrace();
    $args = func_get_args();
    $data = array(
        'data' => $args,
        'debug' => array(
            'file' => $debug[0]['file'],
            'line' => $debug[0]['line'],
        )
    );
    Dumper::dump($data, 10);
    die();
}
