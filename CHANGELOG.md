# v2

Works with PHP 8.1


### v2.0.1

Fix: `IsIntEnumType` was the only type left not using a static `all` method. This has been fixed now.


### v2.0

Dev: Upgrade to PHP 8.1. Note that anything below PHP 8.1 is no longer supported.


# v1

Works with PHP 7.3 and above.
Tested with PHP 8.1.


### v1.1

Feature: Added the following types:
- `IsArrayEnumType`
- `IsFloatType`
- `IsIntArrayEnumType`
- `IsIntStringMapType`
- `IsIntType`
- `IsStringArrayEnumType`
- `IsStringType`
- `IsEmailType`
- `IsCollectionType`

Feature: Added `fromNull` methods to `IsStringEnumType` and `IsIntEnumType`.

Dev: Added unit and mutation tests which can be run with Docker.


### v1.0

Feature: Basic `IsIntEnumType` and `IsStringEnumType`