# Description

Simple PHP AB Tester.

Main functionality is done by copy/pasting this directory AND using an if function.

Extra functionality is available here: 「ab-test-id standard」.

## Reason for Making

PHP AB Testers already exist.
However, they often require composer installation, and don't abstract the important parts.
This library aims to be simple and do what is desired with no building, programming, and little need for adding configuration.

## Specification

- Allow for randomization by probability
- Allow for more than 2
- Allow for manual testing using private GET key for override
- Disallow direct access

## Usage

### Via _AB directory

1. Copy _AB template directory to parent directory where being used.
2. Add original and any other AB target files to _AB/inputs/.
3. Optionally(※However always change "manualAccessKey" in settings.json at least for the first time.) make any desired changes to the _AB/settings.json file.
4. Replace target file with "<?php require_once(__DIR__ . "_AB/index.php");"
5. Open! To manually override settings, use ?manualAccessKey=[MANUAL_ACCESS_KEY]&target=[TARGET_FILE_NAME].

### Via Utility functions

- See 「ab-test-id standard」.

## Settings

[._AB/settings.json](._AB/settings.json)

- manualAccessKey: The key for allowing manual access.
- randomization: How to handle randomization in selection of pages. Falsy(false, "", ...): Equal chance for all. Map of filename to number to map probabilities. Chance is calculated from total of all probabilities.
- debug: Set debug to true to output useful information. Mainly error information.

## Loading utility functions

- If using _AB directory in same directory: `require_once(__DIR__ . "_AB/index.php");`
- If repository is in same directory: `require_once(__DIR__ . '/simple-php-ab-tester/src/utilities.php');`
- If loaded via composer: `require_once(__DIR__ . '/vendor/simple-php-ab-tester/src/utilities.php');`

## Other implementations

### ab-test-id standard

- This library provides a PHP utility function to perform AB testing. Although it is simple to recreate, it is implemented [here](src\utilities.php) because of being related and using PHP, and to standardize a way to do in PHP.
  - Load(Do once per app): `require_once(__DIR__ . '/simple-php-ab-tester/src/utilities.php');`
  - Check via GET: `if (getABTestId('MYID')) { ... } else { ... }`
  - Check via SESSION:
    - Start: `startABTestSession()`
    - Check: `if(isInABTestSession()) { ... } else { ... }`
    - End: `endABTestSession()`
- The above implementation is implemented in HTML, JS, in the following private repository(※ MAY consider making public upon popular request.): [private-js-code/src/classes/ab-tester](https://gitlab.com/dammyg/private-js-code/-/blob/master/src/classes/ab-tester.js).
- The above uses "ab-test-id" as the GET key.

## Examples

Create AB test using utility functions with existing file:

```php
// Original file: Rename to _[ORIGINAL_FILE_NAME].php
// ...

// AB test file: _AB_[ORIGINAL_FILE_NAME].php
// ...

// New file: Replace [ORIGINAL_FILE_NAME].php
require_once(__DIR__ . '/simple-php-ab-tester/src/utilities.php');
if (getABTestId('MYID')) { // ID SHOULD BE SECRET AND HARD ENOGUH TO BREAK SO CAN NOT LEAK DEV ISSUES AND POSSIBLE SECURITY ISSUES. 
    require_once(__DIR__ . '/_AB_[ORIGINAL_FILE_NAME].php');
} else {
    require_once(__DIR__ . '/_[ORIGINAL_FILE_NAME].php');
}
```

## Testing

This library can be tested using the following manual process:

- Although not required, consider copying this entire repository, and use _AB in place.
- Follow [Usage], using your server with PHP available, and adding the files from [test/files](./test/files/) into the inputs directory.
- Place [test/index.php](./test/index.php) into the directory at the same level as _AB. ※It is perfectly OK to rename index.php.
- Go to the PHP file added previously, and check that it correctly alternates between echoing different file names.
- Test manual access key: `?manualAccessKey=[MANUAL_ACCESS_KEY]&target=[TARGET_FILE_NAME]`.
- Test randomization settings.

## License

[LICENSE](./LICENSE)
