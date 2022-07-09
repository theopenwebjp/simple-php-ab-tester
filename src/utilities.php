<?php

/*
PHP: Use GET parameter. Same "ab-test-id". No special functionality, just conditionals checking GET environment variable. Provide example.
*/

/**
 * URL Examples:
 * @example https://theopenweb.info
 * @example https://theopenweb.info?ab-test-id
 * @example https://theopenweb.info?ab-test-id=MYID
 * 
 * Implementation Examples:
 * @example if (getABTestId('MYID')) { ... } else { ... }
 */
function getABTestId() {
    $PARAM_NAME = 'ab-test-id';
    $abTestId = isset($_GET[$PARAM_NAME]) ? $_GET[$PARAM_NAME] : null;
    return $abTestId;
}

// AB Test Sessions.
// For usage when can NOT pass in GET parameter each time.
// For example: In automated testing, redirects, POST and other non-GET requests.
$SESSION_KEY = 'ab-test';
$DEFAULT_KEY = 'default';

/**
 * If $key is empty, tests for any keyed AB test session.
 */
function isInABTestSession($key = '') {
    global $DEFAULT_KEY;
    global $SESSION_KEY;
    if (!$key) {
        $key = $DEFAULT_KEY;
    }
    return isset($_SESSION) && isset($_SESSION[$SESSION_KEY]) && isset($_SESSION[$SESSION_KEY][$key]) && $_SESSION[$SESSION_KEY][$key];
}

/**
 * Starts AB test session.
 * If no $key is passed, will use default key decided internally. SHOULD then not pass key to any other AB Test session function.
 * returns true if success, false if failure.
 */
function startABTestSession($key = '') {
    global $DEFAULT_KEY;
    global $SESSION_KEY;
    if (!$key) {
        $key = $DEFAULT_KEY;
    }
    try {
        session_start();
        if (!isset($_SESSION[$SESSION_KEY])) {
            $_SESSION[$SESSION_KEY] = [];
        }
        $_SESSION[$SESSION_KEY][$key] = true;
        return true;
    } catch (\Exception $err) {
        return false;
    }
}

/**
 * returns true if success, false if failure.
 */
function endABTestSession($key = '') {
    global $DEFAULT_KEY;
    global $SESSION_KEY;
    if (!$key) {
        $key = $DEFAULT_KEY;
    }
    if (isset($_SESSION) && isset($_SESSION[$SESSION_KEY]) && isset($_SESSION[$SESSION_KEY][$key]) && $_SESSION[$SESSION_KEY][$key]) {
        $_SESSION[$SESSION_KEY][$key] = false;
        unset($_SESSION[$SESSION_KEY][$key]);
        return true;
    } else {
        return false;
    }
}
