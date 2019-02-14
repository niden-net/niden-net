---
layout: post
title: Test Driven Development
date: 2009-11-09T23:45:00.000Z
tags:
  - php
  - tdd
  - test driven development
  - phpunit
  - how-to
image: '/assets/files/2009-11-24-tdd.png'
---
I am curious by nature. This is probably one of my best attributes as a person, since I can never rest until I find out 'what this thing is' or 'what is it used for'. This curiosity comes in handy in the programming world.

By being curious, one cannot rest on their laurels and will always explore new ways, learn new things, revise old habits and in short one will become a better programmer. This of course applies to all aspects of life, not just programming.

One day while I was reading some of my favorite blogs, I stumbled upon a *radical* post by [Miško Hevery](https://misko.hevery.com/). His site seemed really interesting but it was only when I saw his talks on YouTube that I started looking into this in more detail. I watched some of Miško's presentations ([The Clean Code Talks: Unit Testing](https://www.youtube.com/watch?v=wEhu57pih5w&amp;feature=PlayList&amp;p=ED6CA927B41FF5BD&amp;index=1), [The Clean Code Talks: Don't Look for Things](https://www.youtube.com/watch?v=RlfLCWKxHJ0&amp;feature=PlayList&amp;p=ED6CA927B41FF5BD&amp;index=2"), [The Clean Code Talks: Inheritance, Polymorphism and Testing](https://www.youtube.com/watch?v=4F72VULWFvc&amp;feature=PlayList&amp;p=ED6CA927B41FF5BD&amp;index=0), [The Clean Code Talks: Clean State and Singletons](https://www.youtube.com/watch?v=-FRm3VPhseI&amp;feature=PlayList&amp;p=ED6CA927B41FF5BD&amp;index=3)) and I think everyone should take the time to watch them.

In his presentations, Miško announces that Singletons are pathological liars and that we should approach programming with a Test Driven Development approach. I also followed the links to his site and there I found excellent articles that changed the way I approach programming. [When to use Dependency Injection](https://misko.hevery.com/2009/01/14/when-to-use-dependency-injection/), [Guide to Writing Testable Code](https://misko.hevery.com/2008/11/24/guide-to-writing-testable-code/) and [Dependency Injection Myth: Reference Passing](https://misko.hevery.com/2008/10/21/dependency-injection-myth-reference-passing/) are some of the blog posts that I would definitely recommend reading.

Reading more and more about Test Driven Development as well as Dependency Injection, I wanted to get as much information as possible prior to diving into coding. I had some questions so I emailed Miško who replied promptly and pointed me to the right direction. Kudos!

#### TDD bug
 
So off I go with a TDD approach in mind. Aaahhhh testing and Q&A! I must admit I have been the worst person in testing and Q&A, always putting it off till the last minute. The problem was not laziness, the problem was twofold:

* I didn't know how and
* I considered tests to take so much time that it would be a total waste of time. Clearly I was wrong there.

Identifying the problem and having a fresh cup of coffee, I loaded Zend Studio on the notebook and decided to start testing – the first step in TDD. I picked one simple class that is used for session management. The class is very small (150 lines inclusive of comments).

The class is below:

```php
class My_Session
{
    /**
     * The name of the storage
     *
     * @var string
     */
    private $_storeName = 'datastore';

    /**
     * Constructor
     */
    public function __construct($store = '')
    {
        $this->init($store);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->destroy();
    }

    public function init($store = '')
    {
        if ($store and !is_object($store)) {
            $this->_storeName = (string)$store  . '_datastore';
        }

        if (!isset($_SESSION[$this->_storeName])) {
            session_start();
            $_SESSION[$this->_storeName]['status'] = true;
        }
    }

    /**
     * Destroys the session handler
     */
    public function destroy()
    {
        $this->init();
        // Unset all of the session variables.
        session_destroy();
        if (isset($_SESSION[$this->_storeName])) {
            unset($_SESSION[$this->_storeName]);
        }
    }

    /**
     * Magic method to retrieve data from the SESSION array
     *
     * @param  string $name Name of the element stored
     * @return mixed        The item stored, null otherwise
     */
    function __get($name)
    {
        $this->init();
        $return = null;
        if (isset($_SESSION[$this->_storeName][$name])) {
            $return = $_SESSION[$this->_storeName][$name];
        }
        return $return;
    }

    /**
     * Magic method to store an element in the SESSION array
     *
     * @param  string $name  The name of the element to store
     * @param  mixed  $value The value of the element to store
     *
     */
    function __set($name, $value)
    {
        $this->init();
        $_SESSION[$this->_storeName][$name] = $value;
    }

    /**
     * Retrieves data from the SESSION array - 
     * calls the magic method
     *
     * @param  string $name Name of the element stored
     * @return mixed        The item stored, null otherwise
     */
    public function get($name)
    {
        $this->init();
        $this->__get($name);
    }

    /**
     * Stores an element in the SESSION array - 
     * calls the magic method
     *
     * @param  string $name  The name of the element to store
     * @param  mixed  $value The value of the element to store
     *
     */
    public function set($name, $value)
    {
        $this->init();
        $this->__set($name, $value);
    }

    /**
     * Dumps the internal array on screen
     *
     */
    public function dump()
    {
        $this->init();
        return print_r($_SESSION[$this->_storeName], true);
    }

    /**
     * Countable implementation
     */
    public function count()
    {
        $this->init();
        return count($_SESSION[$this->_storeName]);
    }
}
```

Pretty simple class.

##### First test

So I wanted to write my first test. Where to begin though? How about instantiation of the object with nothing as the store name? Voila the first test!

```php
    /**
     * Test for creation null value
     */
    public function testCreationNullStoreName()
    {
        $session = new My_Session();
        $session = $this->assertTrue(
            $session instanceof My_Session
        );
        unset($session);
    }
```

I run the test as a PHPUnit test through Zend Studio and I was happy to see green lights everywhere! My test passed! I think that right at that moment something changed. I got infected by the TDD bug :). I did however notice something else. The code completion window was reporting 40% for the session.php file. OK so that tells me that with this test I only covered 40% of the code. I need to have 100% so that I can be 100% sure that my code will not 'break' under some weird circumstances that I haven't foreseen.

##### More tests (initialization)

```php
    public function testCreationEmptyStoreName()
    {
        $session = new My_Session('');
        $session = $this->assertTrue(
            $session instanceof My_Session
        );
        unset($session);
    }

    public function testCreationNotEmptyStoreNameString()
    {
        $session = new My_Session('somestore');
        $this->assertTrue($session instanceof My_Session);
        unset($session);
    }

    public function testCreationNotEmptyStoreNameInteger()
    {
        $session = new My_Session(1);
        $this->assertTrue($session instanceof My_Session);
        unset($session);
    }

    public function testCreationNotEmptyStoreNameObject()
    {
        $object = new DOMDocumentType();
        $session = new My_Session($object);
        $this->assertTrue($session instanceof My_Session);
        unset($session);
    }

    public function testCreationNotEmptyStoreNameBoolTrue()
    {
        $session = new My_Session(true);
        $this->assertTrue($session instanceof My_Session);
        unset($session);
    }

    public function testCreationNotEmptyStoreNameBoolFalse()
    {
        $session = new My_Session(false);
        $this->assertTrue($session instanceof My_Session);
        unset($session);
    }
```

Again tests have passed but I am at 47% coverage. At least now I know that if I instantiate my class with null, '', string, object, integer, float or boolean passed as a parameter in the constructor, I will get an object of `My_Session` back :).

This you might argue that it is an overkill. It might be but after these tests, if anyone was to ask me whether the constructor will return back a `My_Session` object, I would be able to reply with 100% certainty **yes**! It is really difficult to do such a thing without testing your code. How do you know that the user will give you what you expect? Are you going to bury your code under a myriad of `if...then...else` statements hoping that you covered all the possible scenarios? You might be able to do it but I can't. Hence TDD is here to help me.

Back to our tests!

##### Variables

What happens if we store variables? We use the magic `__set` methods to add data to our class. Since our class acts as a proxy to the `$_SESSION` array, we do not need to worry about any internal arrays to hold the information passed. As usual we follow the methodical approach, testing everything in a structured/logical matter (i.e. strings, objects, floats, integers, booleans). Note that the set tests have to be accompanied by get tests since you cannot test what you set without getting it back – the class does not expose the contents stored otherwise. I am creating an extra test for the get to have them separate. It is probably an overkill but this way I know that I follow the *no stone left unturned* method (which is my method :))

```php
    public function testStoreMagicSetVariableString()
    {
        $session = new My_Session('somestore');
        $session->somedata = '1';
        $this->assertSame('1', $session->somedata);
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreMagicGetVariableString()
    {
        $session = new My_Session('somestore');
        $session->somedata = '2';
        $this->assertSame('2', $session->somedata);
        unset($session);
    }

    /**
     * Test for storing
     */
    public function testStoreMagicSetVariableInteger()
    {
        $session = new My_Session('somestore');
        $session->somedata = 1;
        $this->assertSame(1, $session->somedata);
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreMagicGetVariableInteger()
    {
        $session = new My_Session('somestore');
        $session->somedata = 2;
        $this->assertSame(2, $session->somedata);
        unset($session);
    }

    /**
     * Test for storing
     */
    public function testStoreMagicSetVariableFloat()
    {
        $session = new My_Session('somestore');
        $session->somedata = 1.5;
        $this->assertSame(1.5, $session->somedata);
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreMagicGetVariableFloat()
    {
        $session = new My_Session('somestore');
        $session->somedata = 2.5;
        $this->assertSame(2.5, $session->somedata);
        unset($session);
    }

    /**
     * Test for storing
     */
    public function testStoreMagicSetVariableObject()
    {
        $session = new My_Session('somestore');
        $object = new DOMDocumentType();
        $session->somedata = $object;
        $this->assertTrue(
            $session->somedata instanceof DOMDocumentType
        );
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreMagicGetVariableObject()
    {
        $session = new My_Session('somestore');
        $object = new DOMDocumentType();
        $session->somedata = $object;
        $this->assertTrue(
            $session->somedata instanceof DOMDocumentType
        );
        unset($session);
    }
```

OK done with the magic `__get` and `__set` methods! All tests have passed and I have this huge grin on my face. The only thing now is that my code is still not 100% covered – I am at 73%. Since I have also get and set methods (along with the magic ones) working as proxies to the magic `__get` and `__set`, I need to test those too. Basically I will copy and paste the same tests I have used for the magic methods but now using the get/set pair.

```php
    /**
     * Test for storing
     */
    public function testStoreSetVariableString()
    {
        $session = new My_Session('somestore');
        $session->set('somedata', '1');
        $this->assertSame('1', $session->get('somedata'));
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreGetVariableString()
    {
        $session = new My_Session('somestore');
        $session->set('somedata', '2');
        $this->assertSame('2', $session->get('somedata'));
        unset($session);
    }

    /**
     * Test for storing
     */
    public function testStoreSetVariableInteger()
    {
        $session = new My_Session('somestore');
        $session->set('somedata', 1);
        $this->assertSame(1, $session->get('somedata'));
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreGetVariableInteger()
    {
        $session = new My_Session('somestore');
        $session->set('somedata', 2);
        $this->assertSame(2, $session->get('somedata'));
        unset($session);
    }

    /**
     * Test for storing
     */
    public function testStoreSetVariableFloat()
    {
        $session = new My_Session('somestore');
        $session->set('somedata', 1.5);
        $this->assertSame(1.5, $session->get('somedata'));
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreGetVariableFloat()
    {
        $session = new My_Session('somestore');
        $session->set('somedata', 2.5);
        $this->assertSame(2.5, $session->get('somedata'));
        unset($session);
    }

    /**
     * Test for storing
     */
    public function testStoreSetVariableObject()
    {
        $session = new My_Session('somestore');
        $object = new DOMDocumentType();
        $session->set('somedata', $object);
        $this->assertTrue(
            $session->get('somedata') instanceof DOMDocumentType
        );
        unset($session);
    }

    /**
     * Test for retrieving
     */
    public function testStoreGetVariableObject()
    {
        $session = new My_Session('somestore');
        $object = new DOMDocumentType();
        $session->set('somedata', $object);
        $this->assertTrue(
            $session->get('somedata') instanceof DOMDocumentType
        );
        unset($session);
    }
```

##### Bug identified!

There is a problem now. I got red lights from my tests. Despite the fact that the get/set pair are proxies to the `__get`/`__set`. I have a bug somewhere. Checking the unit test reveals the problem:

```php
    public function get($name)
    {
        $this->init();
        $this->__get($name);
    }
```

I never returned the value of the `__get` method and as a result the get returns always null. 8 out of the 23 tests have failed despite the fact that I have 87% code coverage. Modifying the function to

```php
    public function get($name)
    {
        $this->init();
        return $this->__get($name);
    }
```

makes all tests pass and the grin is back on my face.

The beauty of all these tests is that I have made a change in the class that I am testing and although I changed the behavior of one method (returning a value instead of always null) I am again confident that my class will work as I expect it to do. After the change I have corrected a bug and ensured that all the tests have passed. Surprisingly enough this bug has been lurking there for quite some time and it was only until I approached my class with TDD that I found it. The question you should be asking yourself is whether your code has 100% coverage AND it passes all the tests.

##### 100% coverage

The final three tests that I need to create are for specific methods. `dump()`, `count()` and `destroy()`. The tests are as follows:

```php
    /**
     * Test for dump
     */
    public function testDump()
    {
        $session = new My_Session('somestore');
        $session->somedata = 2.5;
        $this->assertTrue(count($session->dump()) > 0);
        unset($session);
    }

    /**
     * Test for count
     */
    public function testGetCount()
    {
        $session = new My_Session('somestore');
        $session->somedata = 2.5;
        $this->assertSame(2, $session->count());
        unset($session);
    }

    /**
     * Test for destroy
     */
    public function testDestroy()
    {
        $session = new My_Session('somestore');
        $session->somedata = 2.5;
        $session->destroy();
        $this->assertSame($session->somedata, null);
        unset($session);
    }
```

Running the above we get to the magic 100% coverage! Now the grin is permanent. I know now for sure that my code is bug free and that it will do what I expect it to do.

#### Conclusion and thoughts:

The above test took me the best part of 2 hours to complete. After numerous failed attempts I did manage to get the first test to run as a PHPUnit test. After that the time spent was more on the code and the tests themselves. The highlight of this exercise was that I found a lurking bug that I wouldn't have found otherwise – at least easily.

Test Driven Development is tedious but only at the beginning. Since I was not used to this kind of development, it took me a lot longer to create each test. In addition to this, since I was thinking about it, I started devising more and more tests. For instance at first I was testing only for strings as input parameters. Later on I added floats, integers, objects etc. into the mix, thus ensuring that my class can handle all data types.

I would encourage everyone to at least give TDD a try. Don't despair when the test is not working. Stick with it, ask questions, spend time on it and you will succeed. Give it at least a fair chance and the rewards will be invaluable! I know I have changed my programming style and am approaching every problem/coding request with TDD in mind. This way I know that my code works thus eliminating future bug fixes which in effect take more time than what I have used (or will use) for initial development.

Some resources that I have used following recommendations from Miško Hevery:

* Dave Astels: [Test Driven Development: A Practical Guide](https://www.amazon.com/Test-Driven-Development-Practical-Guide-Coad/dp/0131016490/ref=sr_1_1?ie=UTF8&amp;s=books&amp;qid=1217181313&amp;sr=1-1)
* Dave Astels: [A Practical guide to eXtreme Programming](https://www.amazon.com/Practical-Guide-eXtreme-Programming-Coad/dp/0130674826/ref=sr_1_2?ie=UTF8&amp;s=books&amp;qid=1217181313&amp;sr=1-2)

Miško writes in his blog:
 
> *My big aha moment in software development came when I learned to do Test Driven Development. The subjective and objective quality of my code has gone through the roof, and I was hooked.*

I think I am at that stage too!!! :)

