# v2

Works with PHP 8.1.

## v2.5

**B/C-Breaking Change**: `isClassCollectionType::className()` is now a `static` abstract method. This means all existing classes using this trait will need to change it from `protected` to `protected static`. No further changes should be necessary.
As a static, it can be used for a wider range of useful convenience methods, like the new `fromRawArray`.

Feature: `IsClassCollectionType` now has a `fromRawArray` method, which allows creating a new array not from instances of the required class, but from raw values which are then automatically converted into the required class. A custom conversion callback can be provided. In order to globally (for that particular class) override the conversion of raw values into target class instances, you can override the protected static method `convertFromRaw(mixed $value) : object`.

Feature: `IsCollectionType` now has `find` and `findIndex` methods, which allows returning a specific element (or index, respectively) based on a custom callback. This means it's no longer needed to convert the class back to an array for the sake of finding a specific element.

Feature: `isEqualTo` and `isNotEqualTo` have been added to `IsStringEnumType`, `IsStringType`, `IsIntType`, `IsIntEnumType` and `IsFloatType`, to be consistent with other traits. `ConversionError` has been introduced, which is thrown when attempting to perform a loose comparison with a value that cannot be converted to the target type.

Feature: Methods for mathematical operations (`add`, `subtract`) and comparisons (`isGreaterThan`, `isGreaterThanOrEqualTo`, `isLessThan`, `isLessThanOrEqualTo` have been added to `IsFloatType` and `IsIntType`.

Feature: `IsFloatType` now has `fromString`, `fromStringOrNull`, `fromNumber`, `fromNumberOrNull`.

Feature: `IsIntEnumType` now has a `fromString`, `fromStringOrNull`

Feature: `isIntType` now has `fromStringOrNull` (besides the pre-existing `fromString`) in order to make it consistent with other traits.

Feature: `IsIntEnumType` now implements the magic `__toString` method, which aids with comparisons and rendering.

Dev: Tests to cover all of the above have been added. Some additional tests to improve pre-existing ones have been added. 

Dev: Every single mutant in the `infection.log` has been checked - they are all false positives. Several came back as `escaped` when in fact, the same manual mutation causes tests to fail. The @covers annotation is correct. It may be a bug in how multi-layer trait inheritance is perceived as covered by Infection. MSI has fallen from 95% to 94% and Covered Code MSI has fallen from 97% to 95% - however, there is nothing that can be done to improve it.


## v2.4

Change: `isEqualTo` and `isNotEqualTo` on `IsIntStringMapType` default to using a strict check, which means the item to compare it to must be of the same class. If you do not want a strict check to happen, you can continue to pass `false` for the `$strictCheck` parameter.

Change: `isEqualTo` and `isNotEqualTo` on `IsCollectionType` (which affects all array types) have a new `$strictCheck` parameter, defaulting to `true`. When it is true, the item to compare to must be of the same class. If you do not want a strict check to happen, you can pass `false` for the `$strictCheck` parameter.


## v2.3

Feature: Added `isEqualTo` and `isNotEqualTo` to `IsIntStringMapType`.
Overall Mutation Code Coverage has risen from 97% to 98%. No value has decreased.

Feature: Added `withValues`, `withoutValues` and `tryWithoutValues` to `IsCollectionType`, allowing to add/remove multiple values with a single method call. MSI has risen from 94% to 95% and Covered Code MSI from 96% to 97%.


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