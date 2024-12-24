# v2

Requires a minimum of PHP 8.1.
Works with PHP 8.4.


## v2.7

### Breaking changes

1) `next()` and `previous()` on `IsCollectionType` no longer return a value.
    - This is because of `\Iterator` requiring `next` to return `void`, and it makes sense for `next` and `previous` to function in the same way.
2) `ConversionError` now extends from `\ValueError` (previously `\RuntimeException`)


### Features

1) Added default classes to be able to use some of the traits instantly without having to create an empty new class each time. The classes added are:
   - `\FireMidge\ValueObject\Generic\AnyCollection`
   - `\FireMidge\ValueObject\Generic\AnyFloat`
   - `\FireMidge\ValueObject\Generic\AnyInteger`
   - `\FireMidge\ValueObject\Generic\AnyString`
   - All of the above classes implement `\JsonSerializable`, and `AnyCollection` also implements the `\Iterator` interface.
   
2) Added default `Percentage` and `Email` classes
   - `\FireMidge\ValueObject\Generic\Percentage`
   - `\FireMidge\ValueObject\Generic\Email`
   
3) New methods on `IsCollectionType`:
    - `merge`
    - `withMerged`
    - `pop`
    - `popMultiple`
    - `split`
    - `shuffle`
    - `withReversedOrder`

4) `jsonSerialize` has been implemented for all types, to aid easy serialisation. 
   - In order for automatic serialisation to happen when `json_encode` is called, the class using these traits has to implement the `JsonSerializable` interface. 
   - The new generic classes all implement it already.


### Improvements

1) Added Psalm annotations in the collection type.
   - This allows for better IDE-internal type hints for methods like `toArray()`, `first()`, `last()` etc. 
   - Usage is showcased in `README.md`
2) More specific error messages for adding or subtracting a value from a float or integer type beyond allowed min/max values.
3) Option to return `$reasons` from a `ConversionError`
4) `InvalidValue` renders different types better, e.g. non-strings are no longer wrapped in double quotes
5) Library is tested against **PHP 8.4**. Worked without changes to the code.
6) Upgraded from PhpUnit 9 to 11.5 and upgraded Infection to 29.0.
    - Changed all docblock annotations to attributes
    - Infection's output is vastly improved, removing the vast majority of false positives. MSI has changed from 97% to 99%, Mutation Code Coverage from 98% to 99% and Covered Code MSI from 98% to 100%.


## v2.6

### Features

1) Added new type `IsClassArrayEnumType`, which is a convenience type between `ArrayEnumType` and `ClassCollectionType`.
   - It's used for collections of instances which in turn are enum types, i.e. there is a finite list of valid values. It also offers an easy way to create a new collection holding all valid variations of the underlying enum type (`withAll()`).

2) Added pointer methods to `IsCollectionType`:
   - `current`
   - `previous`
   - `next`
   - `first`
   - `last`
   - This is, by extension, also available to instances using the `IsClassCollectionType`, `IsArrayEnumType`, `IsClassArrayEnumType` and all other related traits. 
   - Unlike the equivalent built-in PHP methods, these new methods return `null` for non-existing elements instead of `false`.


### Improvements
1) Improved `@covers` annotations and re-imported certain traits to improve accuracy of code coverage reports. Added tests for all new functionality as well as addressed all true positives in the Infection log. 
    - MSI improved from 94% to 97% and CCM from 95% to 98%.


## v2.5

### Breaking changes

1) `IsClassCollectionType::className()` is now a `static` abstract method. 
   - This means all existing classes using this trait will need to change it from `protected` to `protected static`. No further changes should be necessary.
   - As a static, it can be used for a wider range of useful convenience methods, like the new `fromRawArray`.


### Features

1) `IsClassCollectionType` now has a `fromRawArray` method
   - This allows creating a new array not from instances of the required class, but from raw values which are then automatically converted into the required class. 
   - A custom conversion callback can be provided. 
   - In order to globally (for that particular class) override the conversion of raw values into target class instances, you can override the protected static method `convertFromRaw(mixed $value) : object`.

2) `IsCollectionType` now has `find` and `findIndex` methods.
   - This allows returning a specific element (or index, respectively) based on a custom callback.
   - This means it's no longer needed to convert the class back to an array for the sake of finding a specific element.

3) `isEqualTo` and `isNotEqualTo` have been added to the following:
   - `IsStringEnumType`
   - `IsStringType`
   - `IsIntType`
   - `IsIntEnumType`
   - `IsFloatType`
   - This was done to be consistent with other traits. 
   - `ConversionError` has been introduced, which is thrown when attempting to perform a loose comparison with a value that cannot be converted to the target type.

4) The following methods for mathematical operations and comparisons have been added to `IsFloatType` and `IsIntType`:
   - `add`
   - `subtract`
   - `isGreaterThan`
   - `isGreaterThanOrEqualTo`
   - `isLessThan`
   - `isLessThanOrEqualTo`

5) `IsFloatType` now has:
   - `fromString`
   - `fromStringOrNull`
   - `fromNumber`
   - `fromNumberOrNull`

6) `IsIntEnumType` now has:
   - `fromString`
   - `fromStringOrNull`

7) `isIntType` now has `fromStringOrNull` (besides the pre-existing `fromString`) in order to make it consistent with other traits.

8) `IsIntEnumType` now implements the magic `__toString` method, which aids with comparisons and rendering.


### Improvements

1) Tests to cover all of the above have been added. Some additional tests to improve pre-existing ones have been added. 

2) Every single mutant in the `infection.log` has been checked - they are all false positives. 
   - Several came back as `escaped` when in fact, the same manual mutation causes tests to fail. 
   - The @covers annotation is correct. It may be a bug in how multi-layer trait inheritance is perceived as covered by Infection. 
   - MSI has fallen from 95% to 94% and Covered Code MSI has fallen from 97% to 95% - however, there is nothing that can be done to improve it.


## v2.4

### Breaking changes

1) `isEqualTo` and `isNotEqualTo` on `IsIntStringMapType` default to using a **strict check**, which means the item to compare it to must be of the same class. If you do not want a strict check to happen, you can continue to pass `false` for the `$strictCheck` parameter.

2) `isEqualTo` and `isNotEqualTo` on `IsCollectionType` (which affects all array types) have a new `$strictCheck` parameter, defaulting to `true`. When it is true, the item to compare to must be of the same class. If you do not want a strict check to happen, you can pass `false` for the `$strictCheck` parameter.


## v2.3

### Features

1) Added `isEqualTo` and `isNotEqualTo` to `IsIntStringMapType`.
   - Overall Mutation Code Coverage has risen from 97% to 98%. No value has decreased.

2) Added `withValues`, `withoutValues` and `tryWithoutValues` to `IsCollectionType`, allowing to add/remove multiple values with a single method call. 
   - MSI has risen from 94% to 95% and Covered Code MSI from 96% to 97%.


## v2.2

### Features

1) It is now possible to also transform values in `IsStringEnumType` before validating.

2) There is a new `fromString` method on `IsIntType`.


### Improvements

1) Instead of using method-based @covers annotations, we now have class-based @covers annotations, which is working SO much better. Now the Infection log and coverage reports are far more accurate.

2) Added more tests, to bring unit test coverage up to 100%.

3) Throwing `LogicException` when trying to validate the length of a string but passing a higher `$minNumber` than `$maxNumber`.


## v2.1

### Features

1) Added the following methods to all the array types (`IsArrayEnumType`, `IsCollectionType`, `IsIntArrayEnumType`, `IsStringArrayEnumType`, `IsClassCollectionType`):
   - `count`: Returns the number of elements inside the array value object.
   - `isEmpty`: Whether the value object contains any elements.
   - `isNotEmpty`: The opposite of `isEmpty`.
   - `isEqualTo`: Whether the value object's elements are equal to the argument. The argument can be another class (with a `toArray` method), an array, or an object with public properties.
   - `isNotEqualTo`: The opposite of `isEqualTo`.
   - `empty`: A factory method to create a new instance with no elements.

2) Added a new value type: `IsClassCollectionType`.
   - This adds on to `IsCollectionType` and makes it easier to create a collection holding an array of objects. It validates whether the items are of a specific class. 
   - By default, it does not allow duplicate values, however this can be overridden.

3) Added a small helper trait `CanBeConvertedToStringArray`
   - It can convert an array type into an array of scalar string values. 
   - Useful when using `IsClassCollectionType` with a class implementing the `__toString` method.

4) Adding duplicate values can now be ignored
   - This is achieved by overriding the `protected static function ignoreDuplicateValues() : bool` method and returning `true`.
   - To be backwards-compatible, it returns `false` by default.
   - When this method returns true, any duplicate values will be ignored and simply not added to the collection; without throwing an exception.

### Improvements

1) Separate exception for duplicate values: `DuplicateValue`.
   - It is backwards compatible as it extends from `InvalidValue`, but it does now allow developers to catch this exception separately.


### v2.0.1

#### Fixes

1) `IsIntEnumType` was the only type left not using a static `all` method. This has been fixed now.


## v2.0

### Improvements

1) Upgrade to PHP 8.1. Note that anything below PHP 8.1 is no longer supported.


# v1

Works with PHP 7.3 and above.
Tested with PHP 8.1.


## v1.1

### Features

1) Added the following types:
   - `IsArrayEnumType`
   - `IsFloatType`
   - `IsIntArrayEnumType`
   - `IsIntStringMapType`
   - `IsIntType`
   - `IsStringArrayEnumType`
   - `IsStringType`
   - `IsEmailType`
   - `IsCollectionType`

2) Added `fromNull` methods to `IsStringEnumType` and `IsIntEnumType`.


### Improvements

1) Added unit and mutation tests which can be run with Docker.


## v1.0

### Features

1) Basic `IsIntEnumType` and `IsStringEnumType`