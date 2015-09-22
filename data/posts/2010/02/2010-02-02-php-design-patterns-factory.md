## Design Patterns in PHP - Series

<img class="post-image" src="{{ cdnUrl }}/files/2010-02-02-design-patterns-factory.png" />

A note about these series. It appears that [Giorgio Sironi](http://giorgiosironi.blogspot.com/) and I had the same idea regarding Design Patterns and blogging about them. He covers the [Factory](http://giorgiosironi.blogspot.com/2010/01/practical-php-patterns-factory-method.html) design pattern thoroughly in his blog post, which is recommended reading.

### The Problem

I started off my IT career as a network administrator. This was back in the good old Novell 3.11 days. After that it was Novell 4.0, Microsoft Servers etc. Following that I got more and more involved with Visual Basic and when Microsoft decided to move everyone to .NET I chose not to follow and ended up coding in PHP.

Since my programming knowledge came from within (studying, reading articles, trial and error), the problems that I was facing on a daily basis are the same as almost every developer faces. One particularly challenging problem that I had in the VB days as well as the PHP days was repetition of code and how to eliminate it.

When I started programming for the [Ferrari Fans Fun Forecast](https://ffff.niden.net) site I was running the site using my apartment's ADSL line. In the beginning there were only 20 users or so, therefore that setup was fine. The scripts were VBScript against a local instance of Microsoft SQL Server. Later on though, I switched to PHP while keeping Microsoft SQL Server.

### Initial implementation

I knew that I would have to change my scripts later on to work against MySQL since I was to change the hosting of the site. Through laziness or poor design (you can pick either or both :)) I chose to create a class for Microsoft SQL and later on I would just change it to the MySQL. It seemed the easiest thing to do at the time.

So my class was something like:

```php
 class DbMSSQL
{
    private $_conn = null;

    public function connect()
    {
        // Connect
        // This is where we have the connection parameters
        include_once 'connection.inc.php';

        $this->_conn = mssql_connect($host, $user, $password);
        if (!$this->_conn) {
            throw new Exception('Cannot connect to the database.');
        }
    }

    public function disconnect()
    {
         // Disconnect
        mssql_close($this->_conn);
    }

    public function selectdb()
    {
         // Select the db
        $db = mssql_select_db($database, $this->_conn);
        if (!$db) {
            throw new Exception('Cannot select the database');
        }
    }

    public function query($sql)
    {
     // Query the db
        $_result = mssql_query($sql);

        if (!$_result) {
            throw new Exception('Error in query : ' . $sql);
        }

        $data = array();

        while ($row = mssql_fetch_assoc($_result)) {
            $data[$row['id']] = $row;
        }

        mssql_free_result($_result);

        return $data;
    }
}
```

Everything worked fine so I did not worry about a thing. A few months later though I was forced to move the database (as I expected) to MySQL. I could not move everything in one go so I had to move some of the tables initially and a week later everything else.

To tackle this requirement I created a second class to handle operations against MySQL. The class that I ended up with was:

```php
class DbMySQL
{
    private $_conn = null;

    public function connect()
    {
        // Connect
        // This is where we have the connection parameters
        include_once 'connection.inc.php';

        $this->_conn = mysql_connect($host, $user, $password);
        if (!$this->_conn) {
            throw new Exception('Cannot connect to the database :' . mysql_error());
        }
    }

    public function disconnect()
    {
         // Disconnect
        mysql_close($this->_conn);
    }

    public function selectdb()
    {
         // Select the db
        $db = mysql_select_db($database, $this->_conn);
        if (!$db) {
            throw new Exception('Cannot select the database : ' . mysql_error());
        }
    }

    public function query($sql)
    {
     // Query the db
        $_result = mysql_query($sql);

        if (!$_result) {
            throw new Exception('Error in query : ' . mysql_error() . "\n" . $sql);
        }

        $data = array();

        while ($row = mysql_fetch_assoc($_result)) {
            $data[$row['id']] = $row;
        }

        mysql_free_result($_result);

        return $data;
    }
}
```

You can easily see the problem here. There is a lot of repetition in the code, not so much as the actual method properties but the methods themselves. Both classes have `connect()`, `disconnect()`, `selectdb()` and `query()` as methods. In reality the code changes only slightly since the call for an operation against Microsoft SQL Server is `mssql_*` while for MySQL is `mysql_*`. During the transition week I was in programming hell. At some point I mixed the class names, I was trying to read and update the wrong server etc. (see [Jani Hartikainen's](http://codeutopia.net/) post about [6 programming project mistakes you should avoid](http://codeutopia.net/blog/2010/01/28/6-programming-project-mistakes-you-should-avoid/) - I did all that!).

That week though taught me that I need to pay more attention in designing rather than going full speed ahead with programming and later on paying the consequences.

After thorough research, I discovered a library that would support both platforms. The library that I found was [ADOdb](http://adodb.sourceforge.net/) which is a perfect example of the Factory Pattern. I used that library later on for a different project, but just looking at the code and understanding the flow of operations as well as the implementation of the pattern itself was invaluable to me.

### Interfaces

First of all I need to explain what an interface is and why we need them. According to [PHP.net](http://php.net/manual/en/language.oop5.interfaces.php):

> Object interfaces allow you to create code which specifies which methods a class must implement, without having to define how these methods are handled.

So imagine an interface something like a graft, a blueprint on what I need to construct. The Interface will have the common methods and properties that I need to implement.

When dealing with database connections to two different database servers (Microsoft SQL Server and MySQL), I can clearly define a few methods that will follow CRUD (Create, Read, Update, Delete). Those are:

* Connect to the database server
* Select database
* Insert record
* Delete record
* Update record
* Select record(s)
* Close connection to the database server

My interface would therefore be:

```php
interface iDatabase
{
    public function connect();
    public function disconnect();
    public function selectdb();
    public function query($sql);
}
```

### Design Patterns - Factory

A class implementing the Factory Pattern is like a car&nbsp;manufacturing&nbsp;plant producing three different cars on the same assembly line. All cars have common characteristics like 4 wheels, 4 doors (well most of them), a steering wheel, a dashboard etc. and all of them perform certain operations i.e. drive, reverse etc.

In my problem earlier I could have used the Factory Pattern to create one class that would have implemented my blueprint, the interface which defines the CRUD operations that I need. So based on the above, the implementation will result in three classes.

#### MSSQL class - stored in the file `Db_mssql.php`

```php
class Db_mssql implements iDatabase
{
    private $_conn = null;

    public function connect()
    {
        // Connect
        // This is where we have the connection parameters
        include_once 'connection.inc.php';

        $this->_conn = mssql_connect($host, $user, $password);
        if (!$this->_conn) {
            throw new Exception('Cannot connect to the database.');
        }
    }

    public function disconnect()
    {
         // Disconnect
        mssql_close($this->_conn);
    }

    public function selectdb()
    {
         // Select the db
        $db = mssql_select_db($database, $this->_conn);
        if (!$db) {
            throw new Exception('Cannot select the database');
        }
    }

    public function query($sql)
    {
     // Query the db
        $_result = mssql_query($sql);

        if (!$_result) {
            throw new Exception('Error in query : ' . $sql);
        }

        $data = array();

        while ($row = mssql_fetch_assoc($_result)) {
            $data[$row['id']] = $row;
        }

        mssql_free_result($_result);

        return $data;
    }
}
```

#### MySQL class - stored in the file `Db_mysql.php`

```php
class Db_mysql implements iDatabase
{
    private $_conn = null;

    public function connect()
    {
        // Connect
        // This is where we have the connection parameters
        include_once 'connection.inc.php';

        $this->_conn = mysql_connect($host, $user, $password);
        if (!$this->_conn) {
            throw new Exception('Cannot connect to the database :' . mysql_error());
        }
    }

    public function disconnect()
    {
         // Disconnect
        mysql_close($this->_conn);
    }

    public function selectdb()
    {
         // Select the db
        $db = mysql_select_db($database, $this->_conn);
        if (!$db) {
            throw new Exception('Cannot select the database : ' . mysql_error());
        }
    }

    public function query($sql)
    {
     // Query the db
        $_result = mysql_query($sql);

        if (!$_result) {
            throw new Exception('Error in query : ' . mysql_error() . "\n" . $sql);
        }

        $data = array();

        while ($row = mysql_fetch_assoc($_result)) {
            $data[$row['id']] = $row;
        }

        mysql_free_result($_result);

        return $data;
    }
}
```

Notice that both these classes are almost identical to the initial implementation shown earlier in this post. The only difference is that they are both implementing the iDatabase interface.

So what is different now? The class that implements the Factory Pattern.

```php
class Db
{
    public static function factory($type)
    {
        $fileName = 'Db_' . strtolower($type) . '.php';
        if (!file_exists($fileName)) {
            throw new Exception('File not found : ' . $fileName);
        }

        $className = 'Db_' . strtolower($type);

        return new $className;
    }
}
```

What this class does now is it allows me to load the relevant database connection class on the fly. If I want a Microsoft SQL connection I would call:

```php
    $mssql = Db::factory('mssql');
```

while for MySQL the command becomes:

```php
    $mysql = Db::factory('mysql');
```

Again since both underlying classes implement the `iDatabase` interface, I know exactly what to expect as far as methods and functionality is concerned from each class.

### Conclusion

The Factory Design Pattern is one of the most powerful design patterns. It provides 'decoupling' i.e. breaks the inherited dependency of a class and its subclasses. It also allows for great flexibility while keeping the same interface for your clients.

[Zend Framework](http://framework.zend.com/) uses the Factory Pattern in [Zend_Db](http://framework.zend.com/manual/en/zend.db.adapter.html). Specifically the example on the site shows:

```php
// We don't need the following statement because the
// Zend_Db_Adapter_Pdo_Mysql file will be loaded for us by
// the Zend_Db factory method.

// require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

// Automatically load class Zend_Db_Adapter_Pdo_Mysql
// and create an instance of it.
$db = Zend_Db::factory(
    'Pdo_Mysql', 
    array(
        'host'     => '127.0.0.1',
        'username' => 'webuser',
        'password' => 'xxxxxxxx',
        'dbname'   => 'test'
    )
);
```
The [Zend_Db](http://framework.zend.com/manual/en/zend.db.adapter.html) factory accepts the name of the adapter used for the database connection as the first parameter while the second parameter is an array with connection specific information. With the use of the Factory Pattern, [Zend_Db](http://framework.zend.com/manual/en/zend.db.adapter.html) exposes a common interface which allows programmers to connect to a number of databases using the same methods. Should in the future the application needs to access a different database, the impact to the developer is minimal - in most cases a change to the adapter name (first parameter of the factory class) is all it takes.
