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

Feature: Added `fromNull` methods to `IsStringEnumType` and `IsIntEnumType`.

Dev: Added unit and mutation tests which can be run with Docker.

### v1.0
Feature: Basic `IsIntEnumType` and `IsStringEnumType`