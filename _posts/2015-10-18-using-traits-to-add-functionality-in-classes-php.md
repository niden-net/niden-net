---
layout: post
title: Using Traits to add more functionality to your classes in PHP
date: 2015-10-18T23:45:00.000Z
tags:
  - php
  - traits
  - phalcon
  - how-to
image: '/assets/files/phalcon-logo.png'
---
> Traits are a mechanism for code reuse in single inheritance languages such as PHP.
>
> A Trait is similar to a class, but only intended to group functionality in a fine-grained and consistent way. It is not possible to instantiate a Trait on its own. It is an addition to traditional inheritance and enables horizontal composition of behavior; that is, the application of class members without requiring inheritance. [Source](https://php.net/manual/en/language.oop5.traits.php)

Traits have been introduced in PHP 5.4.0. However, a lot of developers have not yet embraced them and taken advantage of the power that they offer.

As mentioned above in the snippet of the PHP manual, Traits are a mechanism to reuse code, making your code more [DRY](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself). 

Let's have a look at a real life example of how Traits can help you with your Phalcon project, or any project you might have.

#### Models

With Phalcon, we have model classes which represent pretty much a table in our database, and allows us to interact with a record or a resultset for the needs of our application.

#### Scenario

We have an application where we need to store information about Companies. Each Company can have one or more Customers as well as one or more Employees. We chose to store that information in three different tables.
<img class="post-image" src="/assets/files/2015-10-18-model.png" />

For each Employee or Customer, we need to store their first name, middle name and last name. However we also need to be able to show the full name in this format:

```php
<Last name>, <First Name> <Middle Name>
``` 

#### Using custom getter

In each model we can use a custom getter method in the Phalcon model to calculate the full name of the record.

##### Employee

```php
namespace NDN\Models;

class Employee
{
    ...
    public function getFullName()
    {
        return trim(
            sprintf(
                '%s, %s %s',
                $this->getLastName(),
                $this->getFirstName(),
                $this->getMiddleName()
            )
        );
    }
}
```

##### Customer

```php
namespace NDN\Models;

class Customer
{
    ...
    public function getFullName()
    {
        return trim(
            sprintf(
                '%s, %s %s',
                $this->getLastName(),
                $this->getFirstName(),
                $this->getMiddleName()
            )
        );
    }
}
```

The above introduces a problem. If we want to change the behavior of the `getFullName` we will have to visit both models and make changes to the relevant methods in each model. In addition, we are using the same code in two different files i.e. duplicating code and effort.

We could create a base model class that our `Customer` and `Employee` models extend and put the `getFullName` function in there. However that increases the class extensions and could lead to maintenance nightmares. 

For instance we will have to create the base model class that only `Customer` and `Employee` models extend but what would happen if we need common functionality for other models? We will need to then create another base model class and so on and so forth. If we end up piling all the common functionality into one base model class then we will end up with functions that would not apply to all of our models and thus a maintenance nightmare.  

**NOTE**: We can also use the `afterFetch` method to create a calculated field which will be available for us to use. We can use either the getter or the `afterFetch` like so:

```php
namespace NDN\Models;

class Customer
{
    ...
    public function afterFetch()
    {
        $this->full_name = trim(
            sprintf(
                '%s, %s %s',
                $this->getLastName(),
                $this->getFirstName(),
                $this->getMiddleName()
            )
        );
    }
}
```

#### Traits

We can use a trait to offer the same functionality, keeping our code [DRY](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself). Since a Trait is not a class that can be instantiated by itself, we *attach* it to wherever we need to, in this case the `Employee` and `Customer` models.

```php
namespace NDN\Traits;

trait FullNameTrait
{
    /**
     * Gets the user first/last/med name and formats it in a readable format
     *
     * @return  string
     */
    public function getFullName()
    {
        return trim(
            sprintf(
                '%s, %s %s',
                $this->getLastName(),
                $this->getFirstName(),
                $this->getMiddleName()
            )
        );
    }
}
```

We can *attach* now this trait to the relevant models


##### Employee

```php
namespace NDN\Models;

use NDN\Traits\FullNameTrait;

class Employee
{
    use FullNameTrait;
}
```

##### Customer

```php
namespace NDN\Models;

use NDN\Traits\FullNameTrait;

class Customer
{
    use FullNameTrait;
}
```

Now we can use the `getFullName()` function in our two models to get the full name of the Employee or Customer calculated by the relevant model fields.

```php
// Customer:
// first_name:  John
// middle_name: Mark
// last_name:   Doe

// Prints: Doe, John Mark
echo $customer->getFullName();

// Employee:
// first_name:  Stanley
// middle_name: Martin
// last_name:   Lieber

// Prints: Lieber, Stanley Martin
echo $employee->getFullName();
```

#### Conclusion

Traits can be very powerful and helpful allies, keeping our code very flexible and reusable.
 
Give it a try!