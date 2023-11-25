By default, the same value can be added multiple times to the same instance. To control this behaviour, see the example below:

```php
class Sources
{
    // Other code here...
    
    /**
     * This method is linked to ignoreDuplicateValues() - therefore, it is important what both of them do 
     * in order to determine the eventual behaviour.
     * 
     * Returning `true` here causes a `DuplicateValue` exception to be thrown when duplicate values are added,
     * either via `fromArray` or `withValue` - UNLESS you also return `true` from `ignoreDuplicateValues()`.
     * 
     * Returning `false` here and from `ignoreDuplicateValues()` means the same values can be 
     * added multiple times.
     * 
     * Default: Returns `false` unless overridden.
     */
    protected static function areValuesUnique() : bool
    {
        return true;
    }
    
    /**
     * Returning `true` here means that when something attempts to add the same value to an instance 
     * more than once, any duplicate values will be silently ignored (no exceptions thrown) - this 
     * is the behaviour regardless of what `areValuesUnique` returns.
     * 
     * Default: Returns `false` unless overridden.
     */
    protected static function ignoreDuplicateValues() : bool
    {
        return true;
    }
}

```

If each value can only appear once in the object, you have two options:
- If you want an exception to be thrown when duplicate values are being added (either via `fromArray` or via `withValue`), then override  `protected static function areValuesUnique() : bool` and return `true`. An exception of type `DuplicateValue` will be thrown.
- If you do not want an exception to be thrown but want duplicate values to simply be silently ignored (both in `fromArray` and in `withValue`), override `protected static function ignoreDuplicateValues() : bool` and return `true`. If duplicate values are found, they are only added once to the array.

When both `areValuesUnique` and `ignoreDuplicateValues` return `true`, `ignoreDuplicateValues` takes precedence.
**Note**: In order to perform these duplicate checks, the value object is converted into a string first. Make sure you have the `__toString` method implemented if you use custom classes and want these checks. (If you're using any of the types within this library, `__toString` is already implemented on them.)