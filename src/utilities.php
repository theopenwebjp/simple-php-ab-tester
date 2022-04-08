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
