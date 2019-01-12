---
layout: post
title: Flexible storage in MySQL
date: 2009-11-03T23:45:00.000Z
tags:
  - storage
  - programming
  - mysql
  - how-to
---
We all need data to function. Whether this is information regarding what our body craves at the moment - hence go to the local take-away and get it or cook it - or whether this is electronic data to make our tasks easier, makes no difference.
<img class="post-image" src="{{ site.baseurl }}/files/mysql.gif" />

Storing data in an electronic format is always a challenge. When faced with a new project you always try to out think the project's needs and ensure that you covered all the possible angles of it. Some projects though are plain vanilla since say you only need to enter the customer's name, surname, address and phone. But what happens when you need to enter data that you don't know their type?

This is where flexible storage comes into play. You can develop a database system that will store efficiently data (well within reason) without knowing what the data will be.

Say we need to build an application that will be given to the customer to store data about his contacts, without knowing what the fields the customer needs. Fair enough storing the name, surname, address, phone etc. of the customer are pretty much easy and expected to be features. However what about a customer that needs to store in his/her contacts the operating system the contact uses on their computer? How about storing the contact's favorite food recipe, their car mileage, etc. Information is so diverse that you can predict up to a point what is needed but after that you just face chaos. Of course if the application we are building is intended for one customer then everything is simpler. How about more than one customers are our target audience? For sure we cannot fill the database with fields that will definitely be empty for certain customers.

A simple format to store information can be achieved by storing a type and a value. The first field (`data_type`) will be a numeric one to hold the ID of the field while the second field (`data_value`) will be of `TEXT` type for the "value". The reason for the `TEXT` is because we don't know the size of the data that will be stored there. Indexes on both fields can help with speeding up the searches. If you use MySQL 4+ you can for sure opt for the `FULLTEXT` indexing method than the one used in previous MySQL versions.

We also need a second table to hold the list of our data types (`data_type` field). This table will have 2 columns and will have of course an ID (`AUTOINCREMENT INT`) and a `VARCHAR` column to hold the description of the field.

```sql
CREATE TABLE data_types (
    type_id MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT,
    type_name VARCHAR( 50 ) NOT NULL,
    PRIMARY KEY ( type_id )
);
```

The table to store the data in will be as follows:

```sql
CREATE TABLE data_store (
    cust_id MEDIUMINT( 8 ) UNSIGNED NOT NULL ,
    type_id MEDIUMINT( 8 ) UNSIGNED NOT NULL ,
    field_data TEXT NOT NULL,
    PRIMARY_KEY ( cust_id, type_id )
);
```

And also creating another index:

```sql
ALTER TABLE data_store ADD FULLTEXT (field_data);
```
*(Note that the FULLTEXT support is a feature of MySQL version 4+)*

So what does this table do for us. We need to store the information of Mr. John Doe, 123 Somestreet Drive, VA, USA, +1 (000) 12345678 who likes cats and has a Ford Mustang.

We first add the necessary fields we need to store in our data_types table. These fields for our example are as follows:

**1 - Title**

**2 - Country**

**3 - Favorite animal**

**4 - Car**

The numbers in front are the IDs that I got when entering the data in the table.

Assuming that the customer has a unique id of 1, we are off to store the data in our table. In essence we will be adding 4 records into the `data_store` table for every contact we have. The `cust_id` field holds the unique ID for each customer so that we can match the information to a single contact as a block.

```sql
INSERT INTO data_store
    (cust_id, type_id, field_data)
    VALUES
    ('1', '1', 'Mr.'),
    ('1', '2', 'USA'),
    ('1', '3', 'Cat'),
    ('1', '4', 'Ford Mustang');
```

That's it. Now Mr. John Doe is in our database.

Adding a new field will be as easy as adding a new record in our `data_types` table. Now with a bit of clever PHP you can read the `data_types` table and display the data from the data store field.

We can use the above example to store customer data either as a whole or as a supplement. So for instance in our example we can start by storing the customer ID, first name, surname etc. as fields also in the `data_store` table using a specific data type. On a different angle we can just keep the core data in a separate table (storing the first name, surname, address etc.) and linking that table with the data_store one.

This approach although very flexible it has its disadvantages. The first one is that each record has a `TEXT` field to store data in. This will be a huge overkill for data types that are meant to store boolean values or integers. Another big disadvantage is the search through the table. It is `TEXT` but also it is vertically structured in blocks. So if you need to search for everyone living in the USA you will need to first find the `data_type` representing the `Country` field and then match it to the `field_data` field of the `data_store` table.

**There is no one right way of doing something in programming**. It all depends on the circumstances and of course to the demands of the application we are developing.

This is just another way of storing data.
