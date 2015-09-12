## Let the RDBMS do more than just store data

<img class="post-image" src="{{ cdnUrl }}/files/2013-09-15-mariadb.png" />
<img class="post-image" src="{{ cdnUrl }}/files/phalcon-green.png" />

One of the common "mistakes" that programmers (and have been guilty as charged many a times in the past) is not to use the tools that are available to them to the maximum extent possible.

A common example is using the RDBMS of your choice to only store and retrieve data, without taking advantage of its power and its features to the full extent.

A RDBMS can do much, much more. One can use triggers that can auto update fields (as I will demonstrate in this blog post), log data into tables, trigger cascade deletes etc.; stored procedures can compute complex data sets, joining tables and transforming data; views can offer easier representations of data, hiding complex queries from the actual application. In addition, such features, like stored procedures/views, can offer security enhancements as well as maintainability to an application. Execution for instance can be restricted to particular groups/logins, while changing the stored procedure/view only requires a change on the database layer and not the application itself.

In this blog post I will show you a simple example on how one can transfer some of the processing of an application to the [RDBMS](http://www.mariadb.org/). I am using MariaDB as the [RDBMS](http://www.mariadb.org/) and [PhalconPHP](https://phalconphp.com/) as the PHP framework.

### The RDBMS
Each table of my database has several common fields that are used for logging and reporting as well as recording status.

An example table is as follows

```sql
CREATE TABLE IF NOT EXISTS co_address (
  id             int(11)      unsigned NOT NULL AUTO_INCREMENT,
  address_line_1 varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  address_line_2 varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  address_line_3 varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  region         varchar(6)   COLLATE utf8_unicode_ci DEFAULT NULL,
  post_code      varchar(24)  COLLATE utf8_unicode_ci DEFAULT NULL,
  country        varchar(2)   COLLATE utf8_unicode_ci DEFAULT NULL,
  created_id     int(11)      unsigned NOT NULL DEFAULT '0',
  created_date   datetime              NOT NULL,
  updated_id     int(11)      unsigned NOT NULL DEFAULT '0',
  updated_date   datetime              NOT NULL,
  deleted        tinyint(1)   unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY created_id (created_id),
  KEY created_date (created_date),
  KEY updated_id (updated_id),
  KEY updated_date (updated_date),
  KEY deleted (deleted)
) ENGINE=InnoDB  
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
COMMENT='Holds addresses for various entities' AUTO_INCREMENT=1 ;
```

The fields are:

| Field name   | Description                                     |
|--------------|-------------------------------------------------|
| created_id   | The id of the user that created the record      |
| created_date | The date/time that the record was created       |
| updated_id   | The id of the user that last updated the record |
| updated_date | The date/time that the record was last updated  | 
| deleted      | A soft delete flag                              |


There is not much I can do with the user ids (`created`/`updated`) or the `deleted` column (see also notes below regarding this). However as far as the dates are concerned I can definitely let MariaDB handle those updates.

### Triggers
The work is delegated to triggers, attached to each table.

```sql
--
-- Triggers address
--
DROP TRIGGER IF EXISTS trg_created_date;
DELIMITER //
CREATE TRIGGER trg_created_date BEFORE INSERT ON address
 FOR EACH ROW SET NEW.created_date = NOW(), NEW.updated_date = NOW()
//
DELIMITER ;
DROP TRIGGER IF EXISTS trg_updated_date;
DELIMITER //
CREATE TRIGGER trg_updated_date BEFORE UPDATE ON address
 FOR EACH ROW SET NEW.updated_date = NOW()
//
DELIMITER ;
```
The triggers above update the created_date and updated_date fields automatically upon insert/update.

### Phalcon Model
I needed to make some changes to my model `Address`, in order to allow the triggers to work without interference from the model.

```php
class Model extends PhModel
{
    public function initialize()
    {
        // Disable literals
        $this->setup(['phqlLiterals' => false]);

        // We skip these since they are handled by the RDBMS
        $this->skipAttributes(
            [
                'created_date',
                'updated_date',
            ]
        );

    }

    public function getSource()
    {
        return 'address';
    }

    public function getCreatedDate()
    {
        return $this->created_date;
    }

    public function getUpdatedDate()
    {
        return $this->updated_date;
    }
}
```

By using [skipAttributes](https://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Model.html), I am instructing the Phalcon model not to update those fields. By doing so, I am letting my triggers worry about that data.

### Conclusion
It might seem a very trivial task that I am delegating but in the grand scheme of things, the models of an application can be very complex and have a lot of logic in them (and so might controllers). Delegating some of that logic in the RDBMS simplifies things and also increases performance of the application, which now requires just a bit less computational power.

### NOTES
For a soft delete feature i.e. automatically updating the deleted field when a `DELETE` is called, a trigger will not work. Instead one can use a stored procedure for it. See [this](http://stackoverflow.com/questions/8056964/cancel-delete-with-triggers) Stack Overflow answer.