---
layout: post
title: Spaceships in PHP
date: 2019-04-15T20:40:51.879Z
tags:
  - php
  - sorting
  - usort
  - spaceship
---
PHP is an extremely powerful language with tons of features and functions. This wealth of functionality offered is often overlooked because there are so many ways of doing the same thing.

There are plenty of sorting functions in the PHP world such as [ksort](https://www.php.net/manual/en/function.ksort.php), [sort](https://www.php.net/manual/en/function.sort.php), [asort](https://www.php.net/manual/en/function.asort.php) etc.

Sorting a collection of objects using a custom comparison method can be achieved by the sorting methods mentioned above. Fear not though. [usort](https://www.php.net/manual/en/function.usort.php) to the rescue. According to the PHP manual:

> Sort an array by values using a user-defined comparison function

The user defined function can be anything, however utilizing the spaceship operator with an anonymous function and usort can perform the sorting we need.

Assume we have the following array:

```php
$data = [
    'mary',
    'had',
    'a',
    'little',
    'lamb',
];
```

and sorting the array:

```php
usort(
    $data, 
    function (string $left, string $right) {
        return ($left <=> $right);
    }
);
```
The output of printing out `$data` will be:
```
// 'a'
// 'had'
// 'lamb'
// 'little'
// 'mary'
```

The spaceship operator `<==>` compares the two values left and right. It returns:
* `0` if the values are identical
* `1` if the left value is greater than the right value
* `-1` if the left value is greater than the right value

Using the spaceship operator the anonymous function becomes much simpler without additional branching. `usort` completes the task for us.

The above example is a very simple one but it can be used as a blueprint for sorting more complex structures using `usort` and the spaceship operator.
