<?php

require_once('PEAR.php');

class Testing {
    function runTest ($test) {
        $result = call_user_func($test);

        if ($result === true) {
            return 'OK';
        }

        if (PEAR::isError($result)) {
            return 'ERROR: ' . $result->toString();
        }

        return 'ERROR: Error in test construction';
    }

    function runTests ($tests) {
        foreach ($tests as $test) {
            $result = Testing::runTest($test);
            print $_SERVER['argv'][0] . '::' . $test . ': ' . $result . "\n";
        }
    }
}

