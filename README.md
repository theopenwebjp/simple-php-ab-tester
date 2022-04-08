# Description

Simple PHP AB Tester.

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

1. Copy _AB template directory to parent directory where being used.
2. Add original and any other AB target files to _AB/inputs/.
3. Optionally(※However always change "manualAccessKey" in settings.json at least for the first time.) make any desired changes to the _AB/settings.json file.
4. Replace target file with "<?php require_once(__DIR__ . "_AB/index.php");"
5. Open! To manually override settings, use ?manualAccessKey=[MANUAL_ACCESS_KEY]&target=[TARGET_FILE_NAME].

## Settings

[._AB/settings.json](._AB/settings.json)

- manualAccessKey: The key for allowing manual access.
- randomization: How to handle randomization in selection of pages. Falsy(false, "", ...): Equal chance for all. Map of filename to number to map probabilities. Chance is calculated from total of all probabilities.
- debug: Set debug to true to output useful information. Mainly error information.

## Other implementations

### ab-test-id standard

- This library provides a PHP utility function to perform AB testing. Although it is simple to recreate, it is implemented [here](src\utilities.php) because of being related and using PHP, and to standardize a way to do in PHP.
- The above implementation is implemented in HTML, JS, in the following repository: [private-js-code](https://gitlab.com/dammyg/private-js-code).
- The above uses "ab-test-id" as the GET key.

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
