<?php

$INPUTS_DIR = __DIR__ . "/inputs";
$SETTINGS_FILE = __DIR__ . "/settings.json";

$settings = json_decode(file_get_contents($SETTINGS_FILE), true);

/**
 * @type boolean
 */
$debug = $settings["debug"];

/**
 * @param mixed $val
 */
function debugOutput($val) {
    global $debug;
    if ($debug) {
        echo $val;
    }
}

/**
 * @param string $target This is a filename used as the input target key.
 * returns a full path.
 */
function targetToPath($target) {
    global $INPUTS_DIR;
    return $INPUTS_DIR . '/' . $target;
}

function getDirectoryFiles(string $dir) {
    $dirIterator = new RecursiveDirectoryIterator($dir);
    $rii = new RecursiveIteratorIterator($dirIterator);
    $files = array(); 

    foreach ($rii as $file) {

        if ($file->isDir()){ 
            continue;
        }
        // print_r($file);
        // https://www.php.net/manual/en/splfileinfo.getfilename.php
        // $filename = $file->getPathname(); // FULL PATH
        $filename = $file->getFilename(); // File name part only
        // echo $filename;
        $files[] = $filename;
    }

    return $files;
}

/**
 * @param Record<string, number> $randomizationMap
 * @param int $total
 */
function handleAutomatic($randomizationMap, $total) {
    $PRECISION = 100;
    $randomValue = random_int(0, $PRECISION) / $PRECISION;
    $aggregate = 0;
    foreach($randomizationMap as $path => $value) {
        $standardizedValue = $value / $total;
        $aggregate += $standardizedValue;
        if ($aggregate >= $randomValue) {
            require_once($path);
            break;
        }
    }
}

/**
 * @param boolean|number[] $randomization
 * @param string[] $targets
 */
function handleRandomization($randomization, $targets) {
    /**
     * @type Record<string, number>
     */
    $randomizationMap = [];
    $total = 0;

    $targetCount = count($targets);

    foreach($targets as $target) {
        $path = targetToPath($target);

        $value = null;

        if ($randomization && gettype($randomization) === 'array') {
            if (isset($randomization[$path])) {
                $value = $randomization[$path];
            }
        } else { // Add all
            $value = 1 / $targetCount;
        }

        if ($value !== null) {
            $randomizationMap[$path] = $value;
            $total += $value;
        }
    }

    return [
        'randomizationMap' => $randomizationMap,
        'total' => $total
    ];
}

$targets = getDirectoryFiles($INPUTS_DIR);

/**
 * @param string[] $targets
 * @param string $expectedManualAccessKey
 * @return boolean true if complete and should finish processing.
 */
function handleManualOverride($targets, $expectedManualAccessKey) {
    $hasManualAccessKey = isset($_GET["manualAccessKey"]);
    $manualAccessKey = $hasManualAccessKey ? $_GET["manualAccessKey"] : '';
    $manualAccessTarget = isset($_GET["target"]) ? $_GET["target"] : '';
    $manualAccessKeyMatches = $manualAccessKey === $expectedManualAccessKey;
    if (!$manualAccessKey) {
        // EARLY CONTINUE.
    } else if ($manualAccessKey && !$manualAccessKeyMatches) {
        debugOutput($manualAccessKey);
        // SILENT CONTINUE: NO EXIT OR THROWING BECAUSE AB TESTING SHOULD NOT NEGATIVELY AFFECT THE SITE.
    } else if ($manualAccessKey && !$manualAccessTarget) {
        debugOutput('manual access key BUT NOT manual access target');
    } else if ($manualAccessKeyMatches && in_array($manualAccessTarget, $targets)) {
        // echo "access target OK: " . $manualAccessTarget;
        
        $manualAccessPath = targetToPath($manualAccessTarget);
        require_once($manualAccessPath);
        return true; // END OF EXECUTION BECAUSE FOUND ONLY TARGET
    } else if ($manualAccessKeyMatches && !in_array($manualAccessTarget, $targets)) {
        debugOutput(print_r([ 'manualAccessTarget' => $manualAccessTarget, 'targets' => $targets ], true));
        // Problem with the file name.
    } else {
        //
    }

    return false;
}

try {
    $complete = handleManualOverride($targets, $settings["manualAccessKey"]);
    if (!$complete) {
        $r = handleRandomization($settings["randomization"], $targets);
        // debugOutput(print_r($r, true));
        handleAutomatic($r['randomizationMap'], $r['total']);
    }
} catch (Exception $err) {
    debugOutput(print_r($err, true));
}
