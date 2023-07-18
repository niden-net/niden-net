---
layout: post
title: PHP 8.2 Deprecation of Dynamic Properties
date: 2023-07-18T14:20:18.200Z
tags:
  - php
  - php8
  - php82
  - phalcon
---
With the release of PHP 8.2 in [November 2022][php-releases], [Phalcon][phalcon] and in particular [Zephir][zephir] were adjusted accordingly to support the new code and generate the correct module for this new PHP release.

Everything has been working as expected, all the tests were passing, until our community members reported some deprecation warnings appearing in their logs.

In particular, this was appearing in several spots of the application (or similar messages)

```text
Deprecated: Creation of dynamic property Phalcon\Mvc\View\Engine\Volt::$tag is deprecated in... 
```

### History
In the past, developers could very easily _create_ and assign properties in classes on the fly. This means that the following code was perfectly fine:

```php
<?php

class Invoices
{
    public string $inv_id = 0;
}

// ....

$invoice = new Invoices();
$invoice->inv_id = 1234;
// inv_number does not exist in the class
$invoice->inv_number = 'INV-1234';
```
Utilizing `__get()` and `__set()` in your class, you could prevent that behavior, but it was never enforced. With PHP 8.2 we not get a deprecation warning, and in PHP 9 this will become a fatal error.

### Why?

Dynamic properties have been a great feature in PHP, but at the same time pandora's box. A simple typo in a statement (and I should know, I have had my share of those through the years) could assign data to the wrong variable and have consequences for the application. Such errors/typos could go unnoticed for a long time, until the application would start behaving in a weird way. Of course the testing suite should catch that but not all applications have 100% test coverage, and sadly many applications do not have any tests at all.

Frameworks, such as Phalcon rely on dynamic properties to assign data to the view engine, or to the ORM, or to the DI container.

### What now?

Until PHP 9.0 becomes the norm, we have to adjust our code to avoid the deprecation warnings and rewrite parts of our framework/application to avoid the dynamic properties.

We have three exceptions for this deprecation.

#### AllowDynamicProperties attribute

The #[AllowDynamicProperties] attribute introduced in PHP 8.2, we can instruct PHP to stop emitting this deprecation notice. Even child classes of that class will inherit this behavior.

```php
<?php

#[AllowDynamicProperties]
class Invoices
{
    public string $inv_id = 0;
}

// ....

$invoice = new Invoices();
$invoice->inv_id = 1234;
// inv_number does not exist in the class
$invoice->inv_number = 'INV-1234';
```

#### `stdClass` and its children

`stdClass` already has #[AllowDynamicProperties] attribute defined, so extending `stdClass` would allow dynamic properties

```php
<?php

use stdClass;

class Invoices extends stdClass
{
    public string $inv_id = 0;
}

// ....

$invoice = new Invoices();
$invoice->inv_id = 1234;
// inv_number does not exist in the class
$invoice->inv_number = 'INV-1234';
```

#### `__get()` and `__set()`

If a class has the `__set()` (and eventually the `__get()`) magic methods defined, the deprecation warning will not be emitted.

```php
<?php

use stdClass;

class Invoices extends stdClass
{
    public string $inv_id = 0;
    
    public function __set(string $name, mixed $value): void 
    {
    }
}

// ....

$invoice = new Invoices();
$invoice->inv_id = 1234;
// inv_number does not exist in the class
$invoice->inv_number = 'INV-1234';
```

A much easier way to deal with this would be to use an internal array to handle the dynamic properties, in combination with `__get()` and `__set()`:

```php
<?php

class Invoices
{
    private array $store = [];
    
    public function __get(string $name): mixed 
    {
        return $this->store[$name] ?? null;
    }
    
    public function __set(string $name, mixed $value): void 
    {
        $this->store[$name] = $value;
    }
}

// ....

$invoice = new Invoices();
$invoice->inv_id     = 1234;
$invoice->inv_number = 'INV-1234';
```

### Phalcon

To avoid the issue above, we changed the `Phalcon\Di\Injectable` to extend `stdClass`. This also was the case for `Phalcon\Mvc\Model\Row` which represents records for the ORM. 

Ideally, we would have loved to be able to rewrite our code so that we do not have any issues in the future. Storing the data in an internal array, offers the flexibility of dynamic properties and gets rid of the deprecation warnings.

To add the `#[AllowDynamicProperties]` annotation in Zephir would be a nightmare, and it will require a lot of work, so the `stdClass` solution was the best one.

The fix was [here](https://github.com/phalcon/cphalcon/pull/16376)

[php-releases]: https://www.php.net/releases/index.php
[phalcon]: https://phalcon.io
[zephir]: https://zephir-lang.com
