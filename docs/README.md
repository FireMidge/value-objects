# value-objects
This library provides convenience methods for creating value objects.

You may use the below table to decide which type is best for you.
*"Single Value" means the object will hold a single value, whereas "Array of Values" means the object can hold more than one value.*

|                             | Single Value                                                                                                                        | Array of Values                                                                                                                                                                                   |
|:----------------------------|:------------------------------------------------------------------------------------------------------------------------------------|:--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| List of Valid Values        | [`IsStringEnumType`](#isstringenumtype)<br />[`IsIntEnumType`](#isintenumtype)<br/>[`IsIntStringMapType`](#isintstringmaptype)      | [`IsStringArrayEnumType`](#isstringarrayenumtype)<br />[`IsIntArrayEnumType`](#isintarrayenumtype)<br />[`IsClassArrayEnumType`](#isclassarrayenumtype)<br/>[`IsArrayEnumType`](#isarrayenumtype) |
| Any Value/Custom Validation | [`IsEmailType`](#isemailtype)<br/>[`IsStringType`](#isstringtype)<br />[`IsFloatType`](#isfloattype) <br/>[`IsIntType`](#isinttype) | [`IsClassCollectionType`](#isclasscollectiontype)<br />[`IsCollectionType`](#iscollectiontype)                                                                                                    |

## Quality Control

The following table is updated with each code update and is generated with the help of PhpUnit (unit testing tool) and Infection (mutation testing tool):

| &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; | Percentage               | Description                                                                                                                                                                                                                                                                                                                           |
|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|:-------------------------|:--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Code Coverage                                                                                                                                                                                                                                                | ![100%](docs/img/cc.png) | How many methods have been fully covered by tests.                                                                                                                                                                                                                                                                                    |
| Mutation Score Indicator                                                                                                                                                                                                                                     | ![97%](docs/img/msi.png) | Indicates how many generated mutants were detected. *Note that some mutants are false positives.*                                                                                                                                                                                                                                     |
| Mutation Code Coverage                                                                                                                                                                                                                                       | ![98%](docs/img/mcc.png) | Should be in the same ballpark as the normal code coverage. Formula: `(TotalMutantsCount - NotCoveredByTestsCount) / TotalMutantsCount`                                                                                                                                                                                               |
| Covered Code MSI                                                                                                                                                                                                                                             | ![98%](docs/img/ccm.png) | This is the MSI (Mutation Score Indicator) for code that is actually covered by tests. It shows how effective the tests really are. Formula: `TotalDefeatedMutants / (TotalMutantsCount - NotCoveredByTestsCount)`. *Note that for some reason, Infection may report some mutants not being covered by tests when they actually are.* |


## IsStringEnumType

Use this type when there is a set of fixed valid values, and your object represents a single value.

*If there is a set of fixed valid values but your object represents an array of values, use [`IsStringArrayEnumType`](#isstringarrayenumtype).*

Example:
```php
class Season
{
    use IsStringEnumType;

    public const SPRING = 'spring';
    public const SUMMER = 'summer';
    public const AUTUMN = 'autumn';
    public const WINTER = 'winter';

    public static function all() : array
    {
        return [
            self::SPRING,
            self::SUMMER,
            self::AUTUMN,
            self::WINTER,
        ];
    }
}
```

Usage:
```php
$spring = Season::fromString(Season::SPRING);
```

## IsIntEnumType

Use this type when there is a set of fixed valid values, and your object represents a single value.

*If there is a set of fixed valid values but your object represents an array of values, use [`IsIntArrayEnumType`](#isintarrayenumtype).*

Example:
```php
class Status
{
    use IsIntEnumType;
    
    public const INFORMATION  = 1;
    public const SUCCESS      = 2;
    public const REDIRECTION  = 3;
    public const CLIENT_ERROR = 4;
    public const SERVER_ERROR = 5;

    public static function all() : array
    {
        return [
            self::INFORMATION,
            self::SUCCESS,
            self::REDIRECTION,
            self::CLIENT_ERROR,
            self::SERVER_ERROR,
        ];
    }
}
```

Usage:
```php
$success = Status::fromInt(Status::SUCCESS);
```

## IsEmailType

Use this type when the value represents a single e-mail address.
This trait uses [`IsStringType`](#isstringtype) under the hood but performs standard e-mail validation.

Example:

```php
class Email
{
    use IsEmailType;
}
```

Usage:
```php
$email = Email::fromString('hello@there.co.uk');
```

## IsStringType

Use this type when the value represents a single string value, but there is no fixed set of valid values.

If you are expecting an e-mail address, you can use the [`IsEmailType`](#isemailtype) trait instead, which will perform format validation checks.


### Validation

To provide custom validation, override `protected function validate(string $value) : void`.

If you want to only validate the length of the string, you can call `validateLength(string $value, ?int $minLength = null, ?int $maxLength = null) : void` inside the `validate` method.


### String transformation

If you want to transform the input value but not fail validation, override `protected function transform(string $value) : string`.

There are 3 convenience methods available that you can call inside `transform` if you want:
- `trimAndLowerCase(string $value)`
- `trimAndUpperCase(string $value)`
- `trimAndCapitalise(string $value)`

Example:

```php
class ProductName
{
    use IsStringType;

    protected function transform(string $value) : string
    {
        return $this->trimAndCapitalise($value);
    }

    protected function validate(string $value) : void
    {
        $this->validateLength($value, 2, 50);
    }
}
```

Usage:
```php
// $productName will be 'Orange juice'
$productName = ProductName::fromString('  orange juice');
```

## IsIntType

Use this type when the value represents a single integer value, but there is no fixed list of valid values, or it is not feasible to write up each valid value.


### Validation

You can provide custom validation rules by overriding `protected function validate(int $value) : void`. By default, it will validate that the value is a positive integer.

If you only want to validate that a value is between a certain minimum and maximum value, override `protected static function minValidValue() : ?int ` and `protected static function maxValidValue() : ?int`. Returning `NULL` from either means there is no limitation to the minimum or the maximum value respectively.

Example:

```php
class Percentage
{
    use IsIntType;

    protected static function minValidValue() : ?int
    {
        return 0;
    }

    protected static function maxValidValue() : ?int
    {
        return 100;
    }
}
```

Another example, for a value without any limitations:
```php
class Balance
{
    use IsIntType;

    protected static function minValidValue() : ?int
    {
        return null;
    }
}
```

Another example, for a value which has no upper limit but may never be below 5.
```php
class Investment
{
    use IsIntType;

    protected static function minValidValue() : ?int
    {
        return 5;
    }
    
    // It is not necessary to add this in as this is the default.
    protected static function maxValidValue() : ?int
    {
        return null;
    }
}
```

Another example which only allows odd values:
```php
class OddIntType
{
    use IsIntType;

    protected function validate(int $value) : void
    {
        if ($value % 2 === 0) {
            throw new InvalidValue(sprintf('Only odd values allowed. Value provided: %d', $value));
        }
    }
}
```

Usage:
```php
$percentage = Percentage::fromInt(78);
```

## IsFloatType

Use this type when the value represents a single float value.


### Validation

You can provide custom validation rules by overriding `protected function validate(float $value) : void`. By default, it will only validate the float is above 0, but you can change this to allow unlimited values by overriding `minValidValue`.

If you only want to validate that a value is between a certain minimum and maximum value, override `protected static function minValidValue() : ?float ` and `protected static function maxValidValue() : ?float`. Returning `NULL` from either means there is no limitation to the minimum or the maximum value respectively.

Example, which allows a value between 0 and 100, and which automatically crops any decimal points after the 3rd:
```php
class Percentage
{
    use IsFloatType;

    protected static function minValidValue() : ?float
    {
        return 0;
    }

    protected static function maxValidValue() : ?float
    {
        return 100;
    }
    
    protected function transform(float $value) : float
    {
        return round($value, 2);
    }
}
```

Usage:
```php
// $percentage will be 78.58
$percentage = Percentage::fromFloat(78.578);
```

## IsIntStringMapType

Use this type if the value represents a single value which can be mapped between an integer and a string.

This may be useful when you e.g. store a value in the database as an integer (for faster indexing), but convert it to a string for a public API (for better readability).

Example:

```php
class Season
{
    use IsIntStringMapType;

    protected static function provideMap() : array
    {
        return [
            1 => 'spring',
            2 => 'summer',
            3 => 'autumn',
            4 => 'winter',
        ];
    }
}
```

Usage:
```php
// Returns 'summer'
$label = (Season::fromInt(2))->toString();

// Returns 4
$intValue = (Season::fromString('winter'))->toInt();
```


## IsIntArrayEnumType

Use this type when the value represents an array of integer values, where each value must be one of a fixed list of values.

Useful when e.g. building filters, allowing to select a number of statuses or IDs (or others) to be included in the result.


### Unique values

If each value can only appear once in the object, you have two options:
- If you want an exception to be thrown when duplicate values are being added (either via `fromArray` or via `withValue`), then override  `protected static function areValuesUnique() : bool` and return `true`. An exception of type `DuplicateValue` will be thrown.
- If you do not want an exception to be thrown but want duplicate values to simply be silently ignored (both in `fromArray` and in `withValue`), override `protected static function ignoreDuplicateValues() : bool` and return `true`. If duplicate values are found, they are only added once to the array.

When both `areValuesUnique` and `ignoreDuplicateValues` return `true`, `ignoreDuplicateValues` takes precedence.

Example:
```php
class Statuses
{
    use IsIntArrayEnumType;
    
    public const INFORMATION  = 1;
    public const SUCCESS      = 2;
    public const REDIRECTION  = 3;
    public const CLIENT_ERROR = 4;
    public const SERVER_ERROR = 5;

    public static function all() : array
    {
        return [
            self::INFORMATION,
            self::SUCCESS,
            self::REDIRECTION,
            self::CLIENT_ERROR,
            self::SERVER_ERROR,
        ];
    }
    
    protected static function areValuesUnique() : bool
    {
        return true;
    }
}
```

Usage:
```php
$statusesToInclude = Statuses::fromArray([Statuses::INFORMATION, Statuses::SUCCESS]);
$allStatuses       = Statuses::withAll();

$statuses = (Statuses::fromArray([]))
    ->withValue(Statuses::SUCCESS)
    ->withValue(Statuses::SERVER_ERROR)
    ->withoutValue(Statuses::SUCCESS);
    
// The difference between tryWithoutValue and withoutValue is that the try method
// will throw an exception if you are trying to remove a value that did not previously
// exist, whereas withoutValue will simply ignore it.
$statusesWithoutSuccess = $statuses->tryWithoutValue(Statuses::SUCCESS);

$containsSuccess = $statusesToInclude->contains(Statuses::SUCCESS);
```


## IsStringArrayEnumType

Use this type when the value represents an array of string values, where each value must be one of a fixed list of values.

Useful when e.g. building filters, allowing to select a number of fields in the result.


### Unique values

If each value can only appear once in the object, you have two options:
- If you want an exception to be thrown when duplicate values are being added (either via `fromArray` or via `withValue`), then override  `protected static function areValuesUnique() : bool` and return `true`. An exception of type `DuplicateValue` will be thrown.
- If you do not want an exception to be thrown but want duplicate values to simply be silently ignored (both in `fromArray` and in `withValue`), override `protected static function ignoreDuplicateValues() : bool` and return `true`. If duplicate values are found, they are only added once to the array.

When both `areValuesUnique` and `ignoreDuplicateValues` return `true`, `ignoreDuplicateValues` takes precedence.

Example:
```php
class UserFieldList
{
    use IsStringArrayEnumType;
    
    public const NAME        = 'name';
    public const EMAIL       = 'email';
    public const STATUS      = 'status';
    public const FRIEND_LIST = 'friendList';

    protected static function all() : array
    {
        return [
            self::NAME,
            self::EMAIL,
            self::STATUS,
            self::FRIEND_LIST,
        ];
    }
}
```

Usage:
```php
$fields = $fieldsFromRequest === null
    ? UserFieldList::withAll()
    : UserFieldList::fromArray($fieldsFromRequest);

$fields    = UserFieldList::fromArray([UserFieldList::NAME, UserFieldList::EMAIL]);
$allFields = UserFieldList::withAll();

$fields = (UserFieldList::fromArray([]))
    ->withValue(UserFieldList::FRIEND_LIST)
    ->withValue(UserFieldList::STATUS)
    ->withoutValue(Statuses::FRIEND_LIST);

$containsFriendList = $statusesToInclude->contains(UserFieldList::FRIEND_LIST);
```


## IsClassArrayEnumType

Use this type when the value represents an array of class instances, and there is a list of valid values. This means the class instances represent enum types.

Example:
```php
class Sources
{
    use IsClassArrayEnumType;

    protected static function className() : string
    {
        return Source::class;
    }
}
```

It is very similar to using [`IsStringArrayEnumType`](#isstringarrayenumtype) or [`IsIntArrayEnumType`](#isintarrayenumtype) with the exception that each item in this array type is a class instance. It means individual items can be added without having to be converted into a scalar type, as in the usage example below:

Usage:
```php
$source = Source::fromString('invitation');

$sources = Sources::empty();
$sources = $sources->withValue($source);
```

Because classes implementing [`IsClassArrayEnumType`](#isclassarrayenumtype) hold objects, you can perform method calls on returned elements, as in the example below:

Usage:
```php
$sources = Sources::withAll(); // $sources now holds an array with ALL possible Source values.

// Compare the first element that was added to $sources:
$sources->first()->isEqualTo(Source::invitation());

// Find a specific value. Returns `null` if the element does not exist in $sources.
$sourceOrNull = $sources->find(fn(Source $src) => $src->isEqualTo(Source::invitation()));

// You can also perform a pre-check whether a specific value exists in the instance of `IsClassArrayEnumType`:
$containsInvitation = $sources->contains(Source::invitation());
```


### Unique values

#include "docs/partials/UNIQUE_VALUES.md"


### Validation

By default, each element is already being validated for being an object, and an instance of the particular class returned by `className()`. However, if you want additional validation to be performed, you can override `protected function validateEach(mixed $value) : void`, which is executed for each value separately, both when instantiating it and when calling `withValue`. Note that this validation will also run before `withoutValue`, `tryWithoutValue` and `contains`, so you are notified when passing something entirely invalid rather than it being silently swallowed. Make sure to also call `parent::validateEach($value);` unless you want to repeat the default validation behaviour in your overridden version.


### From raw values

If you want to instantiate your collection from "raw" values (as opposed to instances of a class) for convenience reasons (whilst internally converting them to the relevant instances), you can use `fromRawValues`.

Example:
```php
$sources = Sources::fromRawArray([
    'invitation',
    'promotion',
    'reference',
]);
```
This works for a conversion to instances that implement `fromString`, `fromInt`, `fromBool`, `fromFloat`, `fromDouble`, `fromNumber` or accept the relevant parameter through their constructor. *Note that input types are NOT converted. That means if you pass a `string`, only the `fromString` factory method will be attempted.*
If none of the above are present or succeed, the trait will attempt to pass the value into the constructor of the target class. (Should this fail as well, a `ConversionError` is thrown.)


#### Custom conversion

If you would like to use the `fromRawValues` method but your target class has neither of the before-mentioned methods or ways of instantiating, you have three options:


##### 1) Provide a custom callback

If you only need to do a custom conversion once, you can provide a callback to the `fromRawValues` method directly.

Example:
```php
$months = CustomEnumArray::fromRawArray([
    'January',
    'May',
    'July',
], fn($v) => CustomClass::fromMonth($v)));
```


###### 2) Override `convertFromRaw`

If you use a custom conversion more than once on the class, you have the option of overriding `protected static function convertFromRaw(mixed $value) : object` to automatically use your custom converter every time `fromRawValues` is called.

Example:

```php
class CustomEnumArray
{
    use IsClassCollectionType {
        IsClassCollectionType::convertFromRaw as private _convertFromRaw;
    }

    protected static function className() : string
    {
        return CustomClass::class;
    }

    protected static function convertFromRaw(mixed $value) : object
    {
        try {
            return static::_convertFromRaw($value);
        } catch (ConversionError) {
            return CustomClass::fromMonth($value);
        }
    }
}
```


##### 3) Implement your own factory method

Since everything is just a trait, you of course have the option of simply creating your own and replace `fromRawValues`. If you want to keep the same name for your own method and change the signature, just alias the trait's method and make it private.


Usage:
```php
$months = Months::fromArray([
    Month::fromString('December'),
    Month::fromString('August'),
    Month::fromString('October'),
]);

// Alternative way of instantiating the enum collection, if the values
// passed can be converted to the target class.
$months = Months::fromRawArray([
    'December',
    'August',
    'October',
];

// Returns 3
$numberOfMonths = $months->count();

// Returns `true`, although strings are passed, as long as `Month` 
// implements the `__toString` method (e.g. via the trait `IsStringType`).
$emailsMatch = $emails->isEqualTo([
   'December',
   'August',
   'October',
]);
```


## IsArrayEnumType

Use this type when the value represents an array of values of a type other than `string`, `integer` or an instance of a specific class (for those we have [`IsStringArrayEnumType`](#isstringarrayenumtype), [`IsIntArrayEnumType`](#isintarrayenumtype) and [`IsClassArrayEnumType`](#isclassarrayenumtype) respectively) and where there is a list of valid values.


### Combination with other types
You can combine this type with any other type, e.g. to get an array of float types, or an array of int enum types, etc. The difference to using a combination of [`IsStringEnumType`](#isstringenumtype) and [`IsArrayEnumType`](#isarrayenumtype) over [`IsStringArrayEnumType`](#isstringarrayenumtype) is that in the former case, each value is a value object, whereas in the latter, each value is just a scalar string. Of course you can also simply use the newer [`IsClassArrayEnumType`](#isclassarrayenumtype), which combines [`IsArrayEnumType`](#isarrayenumtype) and [`IsClassCollectionType`](#isclasscollectiontype), allowing you to hold an instance of value objects. See [`IsClassArrayEnumType`](#isclassarrayenumtype) for more information.


### Unique values

#include "docs/partials/UNIQUE_VALUES.md"


### Validation

You can provide custom validation by overriding `protected function validateEach(mixed $value) : void`, which is executed for each value separately, both when instantiating it and when calling `withValue`. Note that this validation will also run before `withoutValue`, `tryWithoutValue` and `contains`, so you are notified when passing something entirely invalid rather than it being silently swallowed.

Example:
```php
/**
 * @method static withValue(Status $addedValue)
 * @method static tryWithoutValue(Status $value)
 * @method static contains(Status $value)
 */
class StatusList
{
    use IsArrayEnumType;

    protected static function all() : array
    {
        return array_map(function($value) {
            return Status::fromInt($value);
        }, Status::all());
    }

    protected function validateEach(mixed $value) : void
    {
        if (! is_object($value) || (! $value instanceof Status)) {
            throw InvalidValue::notInstanceOf($value, Status::class);
        }
    }

    protected static function areValuesUnique() : bool
    {
        return true;
    }
    
    protected static function ignoreDuplicateValues() : bool
    {
        return true;
    }
}
```
*Note that the example above is for demonstration purpose only - all of the above functionality comes out of the box by using `IsClassArrayEnumType`.*

Usage:
```php
$statuses    = StatusList::fromArray([Status::SUCCESS, Status::REDIRECTION]);
$allStatuses = StatusList::withAll();

// $duplicateStatusesIgnored will only contain Status::SUCCESS once.
// [ Status::SUCCESS, Status::REDIRECTION ]
// This is because of `ignoreDuplicateValues` returning true.
$duplicateStatusesIgnored = StatusList::fromArray([
    Status::SUCCESS, 
    Status::REDIRECTION,
    Status::SUCCESS,
])

// $newStatuses will only contain one instance of Status::REDIRECTION.
// This is because of `ignoreDuplicateValues` returning true.
$newStatuses = $statuses->withValue(Status::REDIRECTION);
```


## IsClassCollectionType

Use this type when the value represents an array of values, where each value must be an instance of a class and there is **no** finite list of valid values.
If there is a list of valid values, use [`IsArrayEnumType`](#isarrayenumtype).
If the values are not instances of a class, use [`IsCollectionType`](#iscollectiontype).


### Unique values

If each value can only appear once in the object, you have two options:
- If you want an exception to be thrown when duplicate values are being added (either via `fromArray` or via `withValue`), then override  `protected static function areValuesUnique() : bool` and return `true`. An exception of type `DuplicateValue` will be thrown.
- If you do not want an exception to be thrown but want duplicate values to simply be silently ignored (both in `fromArray` and in `withValue`), override `protected static function ignoreDuplicateValues() : bool` and return `true`. If duplicate values are found, they are only added once to the array.

When both `areValuesUnique` and `ignoreDuplicateValues` return `true`, `ignoreDuplicateValues` takes precedence.


### Validation

You can provide custom validation by overriding `protected function validateEach(mixed $value) : void`, which is executed for each value separately, both when instantiating it and when calling `withValue`. Note that this validation will also run before `withoutValue`, `tryWithoutValue` and `contains`, so you are notified when passing something entirely invalid rather than it being silently swallowed.

Example:

```php
/**
 * @method static withValue(Email $addedValue)
 * @method static tryWithoutValue(Email $value)
 * @method static contains(Email $value)
 */
class EmailCollection
{
    use IsClassCollectionType, CanBeConvertedToStringArray;

    protected static function className() : string
    {
        return Email::class;
    }
}
```


### From raw values

If you want to instantiate your collection from "raw" values (as opposed to instances of a class) for convenience reasons (whilst internally converting them to the relevant instances), you can use `fromRawValues`.

Example:
```php
$emails = EmailCollection::fromRawArray([
    'hello@there.co.uk',
    'lorem@ipsum.it',
    'bass@player.at',
]);
```
This works for a conversion to instances that implement `fromString`, `fromInt`, `fromBool`, `fromFloat`, `fromDouble`, `fromNumber` or accept the relevant parameter through their constructor. *Note that input types are NOT converted. That means if you pass a `string`, only the `fromString` factory method will be attempted.*
If none of the above are present or succeed, the trait will attempt to pass the value into the constructor of the target class. (Should this fail as well, a `ConversionError` is thrown.)


#### Custom conversion

If you would like to use the `fromRawValues` method but your target class has neither of the before-mentioned methods or ways of instantiating, you have three options:


##### 1) Provide a custom callback

If you only need to do a custom conversion once, you can provide a callback to the `fromRawValues` method directly.

Example:
```php
$emails = CustomCollection::fromRawArray([
    'hello@there.co.uk',
    'lorem@ipsum.it',
    'bass@player.at',
], fn($v) => CustomClass::fromDomain(substr($v, strrpos($v, '.') + 1)));
```


###### 2) Override `convertFromRaw`

If you use a custom conversion more than once on the class, you have the option of overriding `protected static function convertFromRaw(mixed $value) : object` to automatically use your custom converter every time `fromRawValues` is called.

Example:

```php
class CustomCollection
{
    use IsClassCollectionType {
        IsClassCollectionType::convertFromRaw as private _convertFromRaw;
    }

    protected static function className() : string
    {
        return CustomClass::class;
    }

    protected static function convertFromRaw(mixed $value) : object
    {
        try {
            return static::_convertFromRaw($value);
        } catch (ConversionError) {
            return CustomClass::fromDomain(substr($value, strrpos($value, '.')+1));
        }
    }
}
```


##### 3) Implement your own factory method

Since everything is just a trait, you of course have the option of simply creating your own and replace `fromRawValues`. If you want to keep the same name for your own method and change the signature, just alias the trait's method and make it private.


Usage:
```php
$emails = EmailCollection::fromArray([
    Email::fromString('hello@there.co.uk'),
    Email::fromString('lorem@ipsum.it'),
    Email::fromString('bass@player.at'),
]);

// Alternative way of instantiating the collection, if the values
// passed can be converted to the target class.
$emails = EmailCollection::fromRawArray([
    'hello@there.co.uk',
    'lorem@ipsum.it',
    'bass@player.at',
];

// Returns ['hello@there.co.uk', 'lorem@ipsum.it', 'bass@player.at']
// This method is provided by the trait `CanBeConvertedToStringArray`
$emailsAsStrings = $emails->toStringArray();

// Returns 3
$numberOfEmails = $emails->count();

// Returns `true`, even though strings are passed. This is because `Email` 
// implements the `__toString` method (via the trait `IsStringType`).
$emailsMatch = $emails->isEqualTo([
   'hello@there.co.uk',
    'lorem@ipsum.it',
    'bass@player.at',
]);
```


## IsCollectionType

Use this type when the value represents an array of values and there is **no** finite list of valid values. If there is a list of valid values, use [`IsArrayEnumType`](#isarrayenumtype) (or any of the more specific variations, e.g. [`IsStringArrayEnumType`](#isstringarrayenumtype) if applicable).


### Combination with other types
You can combine this type with any other type, e.g. to get an array of float types, an array of e-mail addresses, etc.
If you need each value to be an instance of a class, consider using [`IsClassCollectionType`](#isclasscollectiontype) instead.


### Unique values

If each value can only appear once in the object, you have two options:
- If you want an exception to be thrown when duplicate values are being added (either via `fromArray` or via `withValue`), then override  `protected static function areValuesUnique() : bool` and return `true`. An exception of type `DuplicateValue` will be thrown.
- If you do not want an exception to be thrown but want duplicate values to simply be silently ignored (both in `fromArray` and in `withValue`), override `protected static function ignoreDuplicateValues() : bool` and return `true`. If duplicate values are found, they are only added once to the array.

When both `areValuesUnique` and `ignoreDuplicateValues` return `true`, `ignoreDuplicateValues` takes precedence.


### Validation

You can provide custom validation by overriding `protected function validateEach(mixed $value) : void`, which is executed for each value separately, both when instantiating it and when calling `withValue`. Note that this validation will also run before `withoutValue`, `tryWithoutValue` and `contains`, so you are notified when passing something entirely invalid rather than it being silently swallowed.

**It is recommended to set up validation, at least for the value type.**


### Value transformation

If you want to transform the input value but not fail validation, override `protected function transformEach($value)`.

By also using the trait `CanTransformStrings`, you'll get 3 convenience methods that you can call inside `transform` if you want:
- `trimAndLowerCase(string $value)`
- `trimAndUpperCase(string $value)`
- `trimAndCapitalise(string $value)`

Example:

```php
/**
 * @method static withValue(string $addedValue)
 * @method static tryWithoutValue(string $value)
 * @method static contains(string $value)
 */
class ProductNameCollection
{
    use IsCollectionType;
    use CanTransformStrings;

    protected function validateEach(mixed $value) : void
    {
        if (! is_string($value)) {
            throw InvalidValue::invalidType($value, 'string');
        }
    }

    /**
    * @param mixed $value
    * @return mixed
     */
    protected function transformEach($value)
    {
        if (! is_string($value)) {
            return $value;
        }
    
        return $this->trimAndCapitalise($value);
    }
}
```

Usage:
```php
// $productNames will be an instance of ProductNameCollection
// with these values: [ 'Orange juice', 'Soap', 'Shampoo' ]
$productNames = ProductNameCollection::fromArray([
    '  orange juice',
    'soap ',
    'SHAMPOO',
]);
```


