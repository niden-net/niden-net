---
layout: post
title: Fast serialization of data in PHP
date: 2011-11-22T23:45:00.000Z
tags:
  - php
  - performance
  - igbinary
  - serialize
  - json
  - how-to
image: '/assets/files/2011-11-22-serialization.png'
image-alt: Serialization
---
> Serialization is the process of converting a data structure or object state into a format that can be stored and "resurrected" later in the same or another computer environment. [source](https://en.wikipedia.org/wiki/Serialization)

There are a lot of areas where one can use serialization. A couple are:

* in a database (storing an array of options specific to the user),&nbsp;
* in an AJAX enabled application (call to get a status update and display to the user without refreshing the whole page), etc.

Based on the the application, serializing and unserializing data can be a very intensive process and can prove to have a big performance hit on the overall system.

#### Options

The most obvious option for serializing and unserializing data are the [serialize](https://php.net/manual/en/function.serialize.php) and [unserialize](https://php.net/manual/en/function.unserialize.php) PHP functions. A bit less popular are [json_encode](https://secure.php.net/manual/en/function.json-encode.php) and [json_decode](https://secure.php.net/manual/en/function.json-decode.php). There is also a third option, using a third party module that one can easily install on their server. This module is called [igbinary](https://github.com/igbinary/igbinary7).
In this blog post I am comparing the three options, in the hope that it will aid you with your selection of the best option for you so as to increase the performance of your application.

I created a test script that used several arrays of data (strings, integers, floats, booleans, objects, mixed data, all of the data types) to test the speed and size of the serialization and speed of unserialization of each of the three candidate function pairs. I run the same function to serialize or unserialize the data respectively for 1,000,000 times so as to produce the results below.

The script that I have used is listed below:

```php
$testStrings = [
    'AK' => 'Alaska',   
    'AZ' => 'Arizona', 
    'VT' => 'Vermont',
    'VA' => 'Virginia', 
    'WV' => 'West Virginia',
];
 
$testIntegers = [0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 84, 144,];

$testBooleans = [true, true, true, true, false, true, true,];
 
$testFloats = [
    0, 1.1, 1.1, 2.22, 3.33, 5.55, 8.88, 13.13, 21.2121, 34.3434, 
    55.5555, 84.8484, 144.144,
];
 
$testMixed = [
    'one', 
    13     => 'two', 
    0      => 25.46, 
    'four' => 0.007, 
    'five' => true, 
    true   => 42,
);
 
$objectOne             = new \stdClass();
$objectOne->firstname  = 'Leroy';
$objectOne->lastname   = 'Jenkins';
$objectOne->profession = 'Gamer';
$objectOne->status     = 'Legend';
 
$objectTwo         = new \stdClass();
$objectTwo->series = 'Fibonacci';
$objectTwo->data   = $testIntegers;
 
$testObjects = [$objectOne, $objectTwo,];

$maxLoop = 1000000;

$templateEncode = "%s [%s]: Size: %s bytes, %s time to encode\r\n";
$templateDecode = "%s [%s]: %s time to decode\r\n";

set_time_limit(0);

$output = '';

/**
 * Set the source arrays
 */
$allTestData = [
    'str' => $testStrings,
    'int' => $testIntegers,
    'bln' => $testBooleans,
    'flt' => $testFloats,
    'mix' => $testMixed,
    'obj' => $testObjects,
];

$testSources = [
    'strings'  => $testStrings,
    'integers' => $testIntegers,
    'booleans' => $testBooleans,
    'floats'   => $testFloats,
    'mixed'    => $testMixed,
    'objects'  => $testObjects,
    'all'      => $allTestData,
];
 
/**
 * ENCODE DATA
 */

/**
 * Start each test
 */
foreach ($testSources as $area => $source)
{
    /**
     * Start the timer
     */
    $serializeStart = microtime(true);

    for ($counter = 0; $counter < $maxLoop; $counter++)
    {
        serialize($source);
    }

    $serializeEnd = microtime(true);

    $serializeOutput = serialize($source);

    $output .= sprintf(
        $templateEncode,
        'serialize()', 
        $area, 
        strlen($serializeOutput), 
        $serializeEnd - $serializeStart
    );
 
    /**
     * JSON
     */
    $jsonStart = microtime(true);

    for ($counter = 0; $counter < $maxLoop; $counter++)
    {
        json_encode($source);
    }

    $jsonEnd = microtime(true);

    $jsonOutput = json_encode($source);

    $output .= sprintf(
        $templateEncode,
        'json_encode()', 
        $area, 
        strlen($jsonOutput), 
        $jsonEnd - $jsonStart
    );
 
    /**
     * igbinary
     */
    $igbinaryStart = microtime(true);

    for ($counter = 0; $counter < $maxLoop; $counter++)
    {
        igbinary_serialize($source);
    }

    $igbinaryEnd = microtime(true);

    $igbinaryOutput = igbinary_serialize($source);

    $output .= sprintf(
        $templateEncode,
        'igbinary_serialize()', 
        $area, 
        strlen($igbinaryOutput), 
        $igbinaryEnd - $igbinaryStart
    );

    $output .= str_repeat('=', 20) . "\r\n";
}

$output .= str_repeat('=:=', 20) . "\r\n";


/**
 * DECODE DATA
 */

/**
 * Start each test
 */
foreach ($testSources as $area => $source)
{
    /**
     * Start the timer
     */
    $data = serialize($source);

    $serializeStart = microtime(true);

    for ($counter = 0; $counter < $maxLoop; $counter++)
    {
        unserialize($data);
    }

    $serializeEnd = microtime(true);

    $output .= sprintf(
        $templateDecode,
        'unserialize()', 
        $area, 
        $serializeEnd - $serializeStart
    );

    /**
     * JSON
     */
    $data = json_encode($source);

    $jsonStart = microtime(true);

    for ($counter = 0; $counter < $maxLoop; $counter++)
    {
        json_decode($data, true);
    }

    $jsonEnd = microtime(true);

    $jsonOutput = json_encode($source);

    $output .= sprintf(
        $templateDecode,
        'json_decode()', 
        $area, 
        $jsonEnd - $jsonStart
    );

    /**
     * igbinary
     */
    $data = igbinary_serialize($source);

    $igbinaryStart = microtime(true);

    for ($counter = 0; $counter < $maxLoop; $counter++)
    {
        igbinary_unserialize($data);
    }

    $igbinaryEnd = microtime(true);

    $igbinaryOutput = igbinary_serialize($source);

    $output .= sprintf(
        $templateDecode,
        'igbinary_unserialize()', 
        $area, 
        $igbinaryEnd - $igbinaryStart
    );

    $output .= str_repeat('=', 20) . "\r\n";
}
 
echo '' . $output . '</pre>';
```

#### Serializing results

When serializing data we are always concerned about the size of the result but also about the time it took for the data to be serialized.

As far as size is concerned, `json_encode` seems to be producing the smallest result in bytes for most of the tests.

##### Size comparison

![](/assets/files/2011-11-22-figure-1.png)

**Strings**

```bash
serialize() [strings]: Size: 105 bytes, 1.8710339069366 time to encode
json_encode() [strings]: Size: 67 bytes, 1.5691390037537 time to encode
igbinary_serialize() [strings]: Size: 64 bytes, 3.2276048660278 time to encode <==
```

**Integers**

```bash
serialize() [integers]: Size: 121 bytes, 3.0198090076447 time to encode
json_encode() [integers]: Size: 34 bytes, 1.2248229980469 time to encode <==
igbinary_serialize() [integers]: Size: 58 bytes, 2.2877519130707 time to encode
```

**Booleans**

```bash
serialize() [booleans]: Size: 62 bytes, 2.0834550857544 time to encode
json_encode() [booleans]: Size: 39 bytes, 1.0889070034027 time to encode
igbinary_serialize() [booleans]: Size: 27 bytes, 1.8252439498901 time to encode <==
```

**Floats**

```bash
serialize() [floats]: Size: 709 bytes, 27.496570825577 time to encode
json_encode() [floats]: Size: 77 bytes, 5.0476500988007 time to encode <==
igbinary_serialize() [floats]: Size: 142 bytes, 2.4856028556824 time to encode
```

**Mixed**

```bash
serialize() [mixed]: Size: 178 bytes, 6.301619052887 time to encode
json_encode() [mixed]: Size: 54 bytes, 2.0463008880615 time to encode
igbinary_serialize() [mixed]: Size: 50 bytes, 2.3894169330597 time to encode <==
```

**Objects**

```bash
serialize() [objects]: Size: 326 bytes, 4.8698291778564 time to encode
json_encode() [objects]: Size: 148 bytes, 2.4744520187378 time to encode <==
igbinary_serialize() [objects]: Size: 177 bytes, 6.472992181778 time to encode
```

**All data types**

```bash
serialize() [all]: Size: 1567 bytes, 42.437592029572 time to encode
json_encode() [all]: Size: 462 bytes, 9.9569129943848 time to encode <==
igbinary_serialize() [all]: Size: 478 bytes, 18.053789138794 time to encode
```

##### Speed comparison

![](/assets/files/2011-11-22-figure-2.png)

Analyzing the time it took for each test to be completed, we see again that json_encode is the clear winner (highlighted in bold the shortest time for the function).

**Strings**

```bash
serialize() [strings]: Size: 105 bytes, 1.8710339069366 time to encode
json_encode() [strings]: Size: 67 bytes, 1.5691390037537 time to encode <==
igbinary_serialize() [strings]: Size: 64 bytes, 3.2276048660278 time to encode
```

**Integers**

```bash
serialize() [integers]: Size: 121 bytes, 3.0198090076447 time to encode
json_encode() [integers]: Size: 34 bytes, 1.2248229980469 time to encode <==
igbinary_serialize() [integers]: Size: 58 bytes, 2.2877519130707 time to encode
```

**Booleans**

```bash
serialize() [booleans]: Size: 62 bytes, 2.0834550857544 time to encode
json_encode() [booleans]: Size: 39 bytes, 1.0889070034027 time to encode <==
igbinary_serialize() [booleans]: Size: 27 bytes, 1.8252439498901 time to encode
```

**Floats**

```bash
serialize() [floats]: Size: 709 bytes, 27.496570825577 time to encode
json_encode() [floats]: Size: 77 bytes, 5.0476500988007 time to encode
igbinary_serialize() [floats]: Size: 142 bytes, 2.4856028556824 time to encode <==
```

**Mixed**

```bash
serialize() [mixed]: Size: 178 bytes, 6.301619052887 time to encode
json_encode() [mixed]: Size: 54 bytes, 2.0463008880615 time to encode <==
igbinary_serialize() [mixed]: Size: 50 bytes, 2.3894169330597 time to encode
```

**Objects**

```bash
serialize() [objects]: Size: 326 bytes, 4.8698291778564 time to encode
json_encode() [objects]: Size: 148 bytes, 2.4744520187378 time to encode <==
igbinary_serialize() [objects]: Size: 177 bytes, 6.472992181778 time to encode
```

**All data types**

```bash
serialize() [all]: Size: 1567 bytes, 42.437592029572 time to encode
json_encode() [all]: Size: 462 bytes, 9.9569129943848 time to encode <==
igbinary_serialize() [all]: Size: 478 bytes, 18.053789138794 time to encode
```

**Combination**

Having the smallest result in size might not always be the best metric to base the choice of the serialization algorithm. For instance, looking at the results above in the Strings test, `igbinary` produces indeed the smallest result in size (64 bytes) but it takes twice as much to serialize the result in comparison to `json_encode` (3.22 vs. 1.56 seconds) and the size difference is a mere 3 bytes (64 vs. 67).

Similarly, for the Boolean test, `igbinary` produces 27 bytes and json_encode 39 bytes. It does however take `igbinary` nearly 80% more time to produce the result compared to `json_encode`.

For the Floats test the situation is reversed. `json_encode` produces a result that is around 50% smaller than the one of `igbinary` but it takes twice as much time to produce it.

As far as serializing data, in my personal opinion, `json_encode` is the clear winner.

#### Unserializing Results

Unserializing data is equally - and at times - more important than serializing. In many applications, developers sacrifice performance in writing but don't compromise when reading data.

In the tests below once can easily see that `igbinary` is the clear winner. At times the `unserialize` function is very close (or outperforms `igbinary`) but overall, `igbinary` is the the function that unserializes data the fastest.

##### Speed comparison

![](/assets/files/2011-11-22-figure-3.png)

**Strings**

```bash
unserialize() [strings]: 1.8259189128876 time to decode <==
json_decode() [strings]: 2.6482670307159 time to decode
igbinary_unserialize() [strings]: 1.8359968662262 time to decode
```

**Integers**

```bash
unserialize() [integers]: 2.3886890411377 time to decode <==
json_decode() [integers]: 2.8659090995789 time to decode
igbinary_unserialize() [integers]: 2.4441809654236 time to decode
```

**Booleans**

```bash
unserialize() [booleans]: 1.8097970485687 time to decode
json_decode() [booleans]: 2.4416139125824 time to decode
igbinary_unserialize() [booleans]: 1.7585029602051 time to decode <==
```

**Floats**

```bash
unserialize() [floats]: 18.512004137039 time to decode
json_decode() [floats]: 3.7896130084991 time to decode
igbinary_unserialize() [floats]: 2.6730649471283 time to decode <==
```

**Mixed**

```bash
unserialize() [mixed]: 4.6794769763947 time to decode
json_decode() [mixed]: 2.7775249481201 time to decode
igbinary_unserialize() [mixed]: 1.9598047733307 time to decode <==
```

**Objects**

```bash
unserialize() [objects]: 5.5468521118164 time to decode
json_decode() [objects]: 5.7660481929779 time to decode
igbinary_unserialize() [objects]: 5.2672090530396 time to decode <==
```

**All data types**

```bash
unserialize() [all]: 31.01339006424 time to decode
json_decode() [all]: 14.574991941452 time to decode
igbinary_unserialize() [all]: 10.734386920929 time to decode <==
```

#### Conclusion

If your application is mostly focused on reads rather than writes, `igbinary` is the clear winner, since it will unserialize your data faster than the other two functions. If however you are more focused on storing data, `json_encode` is the clear choice.

#### Updates

**2013-03-07**: memcached was not used with igbinary. PHP version for tests was 5.3.1 on a Linux Mint machine with 6GB RAM
**2013-06-14**: Reader Dennis has been kind enough to run the same script on his server and share the results with me. He run the scripts on a i7-3930K, 64GB, Debian Squeeze with the latest version of PHP (5.4.16) and igbinary.

##### Serialize

**Strings**

```bash
serialize() [strings]: Size: 105 bytes, 0.63280701637268 time to encode <== Time
json_encode() [strings]: Size: 67 bytes, 0.78271317481995 time to encode
igbinary_serialize() [strings]: Size: 64 bytes, 0.97228002548218 time to encode <== Size
```

**Integers**
        
```bash
serialize() [integers]: Size: 121 bytes, 1.3659980297089 time to encode
json_encode() [integers]: Size: 34 bytes, 0.46304202079773 time to encode <== Time/Size
igbinary_serialize() [integers]: Size: 58 bytes, 0.65074491500854 time to encode
```

**Booleans**
        
```bash
serialize() [booleans]: Size: 62 bytes, 0.80747985839844 time to encode
json_encode() [booleans]: Size: 39 bytes, 0.27534413337708 time to encode <== Time
igbinary_serialize() [booleans]: Size: 27 bytes, 0.52206611633301 time to encode <== Size
```

**Floats**
        
```bash
serialize() [floats]: Size: 307 bytes, 6.3345258235931 time to encode
json_encode() [floats]: Size: 77 bytes, 3.3697159290314 time to encode <== Time/Size
igbinary_serialize() [floats]: Size: 142 bytes, 0.70451712608337 time to encode
```

**Mixed**
        
```bash
serialize() [mixed]: Size: 105 bytes, 1.4573359489441 time to encode
json_encode() [mixed]: Size: 54 bytes, 0.98674011230469 time to encode
igbinary_serialize() [mixed]: Size: 50 bytes, 0.71359205245972 time to encode <== Time/Size
```

**Objects**
        
```bash
serialize() [objects]: Size: 326 bytes, 2.4085388183594 time to encode
json_encode() [objects]: Size: 148 bytes, 1.6553950309753 time to encode <== Time/Size
igbinary_serialize() [objects]: Size: 177 bytes, 2.1983618736267 time to encode
```

**All**
        
```bash
serialize() [all]: Size: 1092 bytes, 13.614814043045 time to encode
json_encode() [all]: Size: 462 bytes, 7.7341570854187 time to encode <== Size
igbinary_serialize() [all]: Size: 478 bytes, 5.6470530033112 time to encode <== Time
```

##### Unserialize

**Strings**
        
```bash
unserialize() [strings]: 0.69071316719055 time to decode
json_decode() [strings]: 1.381010055542 time to decode
igbinary_unserialize() [strings]: 0.52063202857971 time to decode <==
```

**Integers**
        
```bash
unserialize() [integers]: 1.0607678890228 time to decode
json_decode() [integers]: 1.4053201675415 time to decode
igbinary_unserialize() [integers]: 0.70937013626099 time to decode <==
```

**Booleans**
        
```bash
unserialize() [booleans]: 0.65101194381714 time to decode
json_decode() [booleans]: 1.0951101779938 time to decode
igbinary_unserialize() [booleans]: 0.49839997291565 time to decode <==
```

**Floats**
        
```bash
unserialize() [floats]: 5.3973641395569 time to decode
json_decode() [floats]: 2.0127139091492 time to decode
igbinary_unserialize() [floats]: 0.75269412994385 time to decode <==
```

**Mixed**
        
```bash
unserialize() [mixed]: 1.5048658847809 time to decode
json_decode() [mixed]: 1.2782678604126 time to decode
igbinary_unserialize() [mixed]: 0.55352306365967 time to decode <==
```

**Objects**
        
```bash
unserialize() [objects]: 2.6635551452637 time to decode
json_decode() [objects]: 3.3167290687561 time to decode
igbinary_unserialize() [objects]: 1.9917018413544 time to decode <==
```

**All**
        
```bash
unserialize() [all]: 11.949328899384 time to decode
json_decode() [all]: 9.9836950302124 time to decode
igbinary_unserialize() [all]: 4.4029591083527 time to decode <==
```
