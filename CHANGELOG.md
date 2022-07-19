# v2

Works with PHP 8.1.


## v2.3

Feature: Added `isEqualTo` and `isNotEqualTo` to `IsIntStringMapType`.
Overall Mutation Code Coverage has risen from 97% to 98%. No value has decreased.


### v2.2

Feature: It is now possible to also transform values in `IsStringEnumType` before validating.

Feature: There is a new `fromString` method on `IsIntType`.

Dev: Instead of using method-based @covers annotations, we now have class-based @covers annotations, which is working SO much better. Now the Infection log and coverage reports are far more accurate.

Dev: Added more tests, to bring unit test coverage up to 100%.

Dev: Throwing LogicException when trying to validate the length of a string but passing a higher $minNumber than $maxNumber.


### v2.1

Feature: Added the following methods to all the array types (`IsArrayEnumType`, `IsCollectionType`, `IsIntArrayEnumType`, `IsStringArrayEnumType`, `IsClassCollectionType`):
- `count`: Returns the number of elements inside the array value object.
- `isEmpty`: Whether the value object contains any elements.
- `isNotEmpty`: The opposite of `isEmpty`.
- `isEqualTo`: Whether the value object's elements are equal to the argument. The argument can be another class (with a `toArray` method), an array, or an object with public properties.
- `isNotEqualTo`: The opposite of `isEqualTo`.
- `empty`: A factory method to create a new instance with no elements.

Feature: Added a new value type: `IsClassCollectionType`.
This adds on to `IsCollectionType` and makes it easier to create a collection holding an array of objects. It validates whether the items are of a specific class. By default, it does not allow duplicate values, however this can be overridden.

Feature: Added a small helper trait `CanBeConvertedToStringArray`, which can convert an array type into an array of scalar string values. Useful when using `IsClassCollectionType` with a class implementing the `__toString` method.

Feature: Adding duplicate values can now be ignored, by overriding the `protected static function ignoreDuplicateValues() : bool` method and returning `true`. To be backwards-compatible, it returns `false` by default.
When this method returns true, then any duplicate values will be ignored and simply not added to the collection; without throwing an exception.

Dev: Separate exception for duplicate values: `DuplicateValue`.
It is backwards compatible as it extends from `InvalidValue`, but it does now allow developers to catch this exception separately.


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