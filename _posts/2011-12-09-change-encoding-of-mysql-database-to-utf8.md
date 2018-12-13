---
layout: post
title: Change the encoding of a MySQL database to UTF8
tags: [php, mysql, encoding, utf8, how-to]
---

#### Overview

As applications grow, so do their audiences. In this day and age, one cannot assume that all the consumers of a web based application will live in a particular region and use only one language. Even if the developer assumes that one country will be served by the particular web application, there are instances that the `latin1` character set will not suffice in storing data.
<img class="post-image" src="{{ site.baseurl }}/files/mysql.gif" />

Therefore, developers and database designers need to implement an encoding on their database that will safely store and retrieve any kind of data, not only latin1 based (i.e. the English alphabet).

For MySQL this encoding is `utf8_general_ci`.

#### The problem

MySQL usually comes with the `latin1_swedish_ci` encoding as a default. This encoding will allow the developer to store data of course but when non latin1 characters need to be stored, there will be a problem. Effectively latin1 encoding will store data in 8 bits but some languages like Japanese, Thai, Arabic, even French or German have special characters that need more space in the storage engine. Trying to store a 16 bit character in a 8 bit space will fail all the time.

**Latin1 based database:**
Input: abcdef...ABCD...#$
Output: abcdef...ABCD...#$

Input: 日本語 ภาษาไทย Ελληνικά
Output: ??? ??????? ????????

To combat this, all you have to do is change the encoding of your database to `utf8_general_ci` and the character set to `utf8`.

#### The solution

I wrote a script in PHP to analyze a database server and produce `ALTER` statements to be executed against your database(s). The script needs to run from a web server that supports PHP.

First of all, the encoding of the database will change with the relevant SQL statement. Following that each table's encoding will change, again with the relevant SQL statement. Finally, each `TEXT`/`VARCHAR`/`CHAR` etc. field's encoding will change towards the target encoding you specify in the configuration section (see below).

The safest way to transform data this way is to first change the field to a `BINARY` field and then change the field to the target encoding and collation.

#### Configuration

There are a few configuration variables that need to be set prior to running the script.

```php
$db_user       = 'username';
$db_password   = 'password';
$db_host       = 'hostname';
$output_folder = '/home/ndimopoulos'; // Do not include trailing slash
$db_name       = 'mysql';             // Leave this one as is

set_time_limit(0);

/**
 * The old collation (what needs to be changed)
 */
$from_encoding = 'latin1_swedish_ci';

/**
 * The new collation (what we will change it to)
 */
$to_encoding = 'utf8_general_ci';

/**
 * The new character set
 */
$new_collation = 'utf8';

/**
 * Add USE <database>; before each statement?
 */
$use_database = TRUE;
```

The `$output_folder` is a folder that is writeable from your web server and it is where the `.sql` files will be created filled with the `ALTER` statements. The script will output one file `<hostname>.sql` which will contain all the `ALTER` statements for all databases. It will also create files for individual databases `<hostname>.<database>.sql`. You can use either the big file or the individual database files. The choice is yours.

The `$from_encoding` is what the script will check. In my script I was checking `latin1_swedish_ci`.
The `$to_encoding` is what we need the encoding to be while the `$new_collation` is the new character set.

The `$use_database` is a flag that will allow you to generate statements such as:

```sql
USE <database>; 
ALTER TABLE <table>.....
```

if it is on, and if off, the statement will be:

```sql
ALTER TABLE <table>.....
```
#### Databases loop

The script opens a connection to the server and runs the `SHOW DATABASES` command. Based on the result returned, it populates an array with the database names.

The script ignores two databases `information_schema` and `mysql`, but editing the $exclude_databases array will allow you to ignore more databases if you need to.

```php
mysql_connect($db_host, $db_user, $db_password);
mysql_select_db($db_name);

$dbs = array();

exclude_databases = array('mysql', 'information_schema',);

/**
 * Get the databases available (ignore information_schema and mysql)
 */
$result = mysql_query("SHOW DATABASES");

while ($row = mysql_fetch_row($result))
{
    if (!in_array($row[0], $exclude_databases))
    {
        $dbs[] = $row[0];
    }
}
```

The database names are stored in an array, so as not to keep the database resource active all the time. Had I not done that, I would have had to use three different resources (one for the database, one for the table and one for the field being checked - three nested loops).

#### Tables loop

The script then loops through the $dbs array and selects each database in turn. Once the database is selected, the `SHOW TABLES` query is run and a $tables array is populated with the names of the tables in that database. At the same time the `ALTER DATABASE` statements are being generated.

```php
mysql_select_db($db);

$db_output = '';

$statement  = "\r\n#-------------------------------------------------\r\n\r\n";
$statement .= "USE $db;\r\n";
$statement .= "\r\n#-------------------------------------------------\r\n\r\n";
$statement .= "ALTER DATABASE $db "
           . "CHARACTER SET $new_collation COLLATE $to_encoding;\r\n";
$statement .= "\r\n#-------------------------------------------------\r\n\r\n";

$db_output .= $statement;
$output    .= $statement;

$tables     = array();

$result = mysql_query("SHOW TABLES");

while ($row = mysql_fetch_row($result))
{
    if (!in_array($row[0], $exclude_tables))
    {
        $tables[] = mysql_real_escape_string($row[0]);
    }
}
```

#### Fields loop

The script then loops through the $tables array and runs the `SHOW FIELDS` query so as to analyze each field.

```php
$fields_modify = array();
$fields_change = array();

$result = mysql_query("SHOW FULL FIELDS FROM `$table`");
while ($row = mysql_fetch_assoc($result)) 
{
    if ($row['Collation'] != $from_encoding)
    {
        continue;
    }
   
    // Is the field allowed to be null?
    $nullable = ($row['Null'] == 'YES') ? ' NULL ' : ' NOT NULL';

    if ($row['Default'] == 'NULL') 
    {
       $default = " DEFAULT NULL";
    } 
    else if ($row['Default']!='') 
    {
       $default = " DEFAULT '" . mysql_real_escape_string($row['Default']) . "'";
    } 
    else 
    {
       $default = '';
    }

    // Alter field collation:
    $field_name = mysql_real_escape_string($row['Field']);

    $fields_modify[] = "MODIFY `$field_name` $row[Type] CHARACTER SET BINARY";
    $fields_change[] = "CHANGE `$field_name` `$field_name` $row[Type] "
                     . "CHARACTER SET $new_collation "
                     . "COLLATE $to_encoding $nullable $default";
}
```

The two arrays generated (`$fields_modify` and `$fields_change` contain the `MODIFY` and `CHANGE` statements of each field. Using implode, we can easily construct the `ALTER` statement.

```php
$statement .= "ALTER TABLE `$table` " 
            . implode(' , ', $fields_modify) . "; \r\n";
$statement .= "ALTER TABLE `$table` " 
            . implode(' , ', $fields_change) . "; \r\n";
```

**Notes**

You can use as mentioned earlier the `$exclude_databases` array to not allow certain databases to be processed. You can also use the `$exclude_tables` array to not allow certain tables to be processed.

The `$exclude_tables_fields` array allows you to exclude a field from being processed. However this is not tied to a database so any database/table that has a field with that particular name will not be processed. With a bit of refactoring you can make the script best work for your needs.

If you set the `$use_database` variable to TRUE then each line in your .sql statements will be prefixed with a '<span style="font-family: 'Courier New', Courier, monospace;">USE <database>;</span>' statement. This is to help the accompanying bash script to execute each statement in the respective database. If you intend on not running this process one statement at a time, you can set this to FALSE. You can then run each database .sql file (or the one that contains all of the statements from all databases) as one single command.

#### Server processing

Now that the relevant `.sql` files have been created, all you have to do is upload them on your web server. There are three ways of actually running the statements against the database.

**Please make sure you backup your data first!**

**Single file processing**

```sh
mysql -h<host> -u<username> -p<password> < /path/to/scripts/<host>.sql
```

The command above will run all the commands generated in the `<host>.sql` file for all databases. It is going to be taxing for your database server and there is no error handling or reporting. You can always pipe the results to an output file (just append "`> /path/to/output/output.txt`" at the end of the command). If this method fails for some reason (MySQL has gone away), it will be difficult to resume; you will need to edit the <host>.sql file to remove the statements that have already been processed.

**Per database**

```sh
mysql -h<host> -u<username> -p<password> <database_name> \
       </path/to/scripts/<host>.<database>.sql
```

The command above will run all the commands generated in the <host>.<database>.sql file for that particular database. This method is similar to the one above. Again you can always pipe the results to an output file (just append "`> /path/to/output/output.txt`" at the end of the command).

**Single file processing (per statement)**

```sh
/path/to/scripts/process.sh
```

All you need to do is edit the process.sh script and change the relevant parameters to match your environment and upload it to your server. The source file that the process.sh script will read has to be generated with `$use_database` set to `TRUE`.

The `process.sh` script is:

```sh
#!/bin/bash

DBUSER=root
DBPASS=1234
DBHOST=localhost
SOURCE="/home/ndimopoulos/host.sql"
LOG="/home/ndimopoulos/conversion.log"

while read line
do
    TIMENOW=`date +%Y-%m-%d-%H-%M`
    echo START $TIMENOW $line
    echo START $TIMENOW $line >> $LOG
    /usr/bin/time -f "%E real,%U user,%S sys" -v -o $LOG -a \
        mysql -h$DBHOST -u$DBUSER -p$DBPASS -e "$line"
    
    TIMENOW=`date +%Y-%m-%d-%H-%M`
    echo END $TIMENOW 
    echo END $TIMENOW >> $LOG

done < $SOURCE

exit 0
```

The script above will start reading the source file (<host>.sql) and execute each statement in turn, using [time](http://en.wikipedia.org/wiki/Time_(Unix)) to measure the time taken to execute that command. The output ends up in a log file which can easily be tailed to view the progress and used later on for analysis. The results of the processing are also sent to the screen. You can change the parameters for the time command to match your needs.

The output will look something like the block below:

```sh
START 2011-12-08-23-46 USE mydatabase; \
     ALTER TABLE `tablename` DEFAULT CHARACTER SET utf8;
 Command being timed: "mysql -uroot -p1234 -e USE mydatabase; \
     ALTER TABLE `tablename` DEFAULT CHARACTER SET utf8;"
 User time (seconds): 0.01
 System time (seconds): 0.00
 Percent of CPU this job got: 0%
 Elapsed (wall clock) time (h:mm:ss or m:ss): 0:01.16
 Average shared text size (kbytes): 0
 Average unshared data size (kbytes): 0
 Average stack size (kbytes): 0
 Average total size (kbytes): 0
 Maximum resident set size (kbytes): 8192
 Average resident set size (kbytes): 0
 Major (requiring I/O) page faults: 0
 Minor (reclaiming a frame) page faults: 610
 Voluntary context switches: 11
 Involuntary context switches: 5
 Swaps: 0
 File system inputs: 0
 File system outputs: 0
 Socket messages sent: 0
 Socket messages received: 0
 Signals delivered: 0
 Page size (bytes): 4096
 Exit status: 0
END 2011-12-08-23-46
```

#### Conclusion

In order for a database to be best prepared to support localization, you need to make sure that the storage will accept any possible character. You can start by creating all your tables and fields with `utf8_general_ci` encoding, but for existing databases and data, you will need to run expensive processing queries on your RDBMS. Ensuring that the data does not get corrupted when performing the transformation process is essential so make sure you backup your databases before trying or running the output statements produced by the `db_alter.php` script.

**PHP script (`db_alter.php`)**

```php
$db_user       = 'username';
$db_password   = 'password';
$db_host       = 'hostname';
$output_folder = '/home/ndimopoulos'; // Do not include trailing slash
$db_name       = 'mysql'; // Leave this one as is

set_time_limit(0);

/**
 * The old collation (what needs to be changed)
 */
$from_encoding = 'latin1_swedish_ci';

/**
 * The new collation (what we will change it to)
 */
$to_encoding = 'utf8_general_ci';

/**
 * The new character set
 */
$new_collation = 'utf8';

/**
 * Add USE <database> before each statement?
 */
$use_database = TRUE;

mysql_connect($db_host, $db_user, $db_password);
mysql_select_db($db_name);

$dbs = array();

$exclude_databases     = array('mysql', 'information_schema',);
$exclude_tables        = array('logs', 'logs_archived',);
$exclude_tables_fields = array('activities');

/**
 * Get the databases available (ignore information_schema and mysql)
 */
$result = mysql_query("SHOW DATABASES");

while ($row = mysql_fetch_row($result)) 
{
    if (!in_array($row[0], $exclude_databases))
    {
        $dbs[] = $row[0];
    }
}

$output = '';
/**
 * Now select each db and start parsing the tables
 */
foreach ($dbs as $db)
{
    mysql_select_db($db);
    $db_output = '';
    
    $statement  = "\r\n#----------------------------------------\r\n\r\n";
    $statement .= "USE $db;\r\n";
    $statement .= "\r\n#----------------------------------------\r\n\r\n";
    $statement .= "ALTER DATABASE $db "
               . "CHARACTER SET $new_collation COLLATE $to_encoding;\r\n";
    $statement .= "\r\n#----------------------------------------\r\n\r\n";
    
    $db_output .= $statement;
    $output    .= $statement;
    $tables     = array();
    
    $result = mysql_query("SHOW TABLES");
    
    while ($row = mysql_fetch_row($result))
    {
        if (!in_array($row[0], $exclude_tables))
        {
            $tables[] = mysql_real_escape_string($row[0]);
        }
    }
    
    /**
     * Alter statements for the tables
     */
    foreach ($tables as $table)
    {
        $statement = '';
        if ($use_database)
        {
            $statement  = "USE $db; ";
        }
        $statement .= "ALTER TABLE `$table` "
                   . "DEFAULT CHARACTER SET $new_collation;\r\n";
        $db_output .= $statement;
        $output    .= $db_output;
    }
    $statement .= "\r\n#----------------------------------------\r\n\r\n";

    $db_output .= $statement;
    $output    .= $statement;
    
    /**
     * Get the fields for each table
     */
    foreach ($tables as $table)
    {
        if (in_array($table, $exclude_tables_fields))
        {
            continue;
        } 
        
        $fields_modify = array();
        $fields_change = array();
        
        $result = mysql_query("SHOW FULL FIELDS FROM `$table`");
        while ($row = mysql_fetch_assoc($result)) 
        {
            if ($row['Collation'] != $from_encoding)
            {
                continue;
            }
            
            // Is the field allowed to be null?
            $nullable = ($row['Null'] == 'YES') ? ' NULL ' : ' NOT NULL';
            
            if ($row['Default'] == 'NULL') 
            {
                $default = " DEFAULT NULL";
            } 
            else if ($row['Default']!='') 
            {
                $default = " DEFAULT '"
                         . mysql_real_escape_string($row['Default']) . "'";
            }
            else 
            {
                $default = '';
            }
            
            // Alter field collation:
            $field_name = mysql_real_escape_string($row['Field']);
            
            $fields_modify[] = "MODIFY `$field_name` $row['Type'] "
                             . "CHARACTER SET BINARY";
            $fields_change[] = "CHANGE `$field_name` `$field_name` $row['Type'] "
                             . "CHARACTER SET $new_collation "
                             . "COLLATE $to_encoding $nullable $default";
        }
        
        if (count($fields_modify) > 0)
        {
            $statement = '';
            if ($use_database)
            {
                $statement = "USE $db; ";
            }
            $statement .= "ALTER TABLE `$table` "
                        . implode(' , ', $fields_modify) . "; \r\n";
            if ($use_database)
            {
                $statement = "USE $db; ";
            }
            $statement .= "ALTER TABLE `$table` "
                        . implode(' , ', $fields_change) . "; \r\n";
            
            $db_output .= $statement;
            $output    .= $statement;
        }
    }
    
    $bytes = file_put_contents(
        $output_folder . '/' . $db_host . '.' . $db . '.sql', $db_output
    );
}
    
$bytes = file_put_contents($output_folder . '/' . $db_host . '.sql', $output);

echo "<pre>$db_host $bytes \r\n$output</pre>";
```

#### Downloads

You can use these scripts at your own risk. Also feel free to distribute them freely - a mention would be nice. Both scripts can be found in my [GitHub](https://github.com/niden).

