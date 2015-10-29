#### Preface

Recently I worked on setting up replication between our AWS RDS instance and a server running as a MySQL slave in our location. Although the task was not difficult, there are quite a few areas that one needs to pay attention to. 
<img class="post-image" src="{{ cdnUrl }}/files/2015-09-29-aws.png" />

In this blog post, I am going to outline the process step by step, so that others can benefit and not lose time trying to discover what went wrong.

**Disclaimer**: [Google](https://google.com), [DuckDuckGo](https://duckduckgo.com), the [AWS forums](https://forums.aws.amazon.com/) and [this](http://www.ruempler.eu/2013/07/07/replicating-aws-rds-mysql-databases-to-external-slaves/) blog post have been invaluable guides to help me do what I needed to do.

#### Setup

* One RDS MySQL or Aurora instance (Master)
* One server running MySQL in your premises (or wherever you want to put it) (Slave)
* Appropriate access to IP of the Slave on the master.

#### Master Setup

There is little to do on our master (RDS). Depending on the database size and update frequency, we will need to set up the maximum retention time for the bin logs. For a very large database we need to set a high number, so that we are able to export the database from the master, import it in the slave and start replication.

Connect to your database and run the following command:

```sql
MySQL [(none)]> call mysql.rds_set_configuration('binlog retention hours', 24);
```
You can use a different number of hours; I am using 24 for this example.

#### Slave Setup

I am assuming that MySQL is installed on the machine that has been designated as the slave, and also that that machine has ample space for the actual data as well as the binary logs that will be created for the replication.

##### Edit `my.cnf`
 
The location of this file is usually under `/etc` or `/etc/mysql`. Depending on your distribution it might be located elsewhere.

```sh
[mysqld]
...

#bind-address = 0.0.0.0

# Logging and Replication
general_log_file  = /logs/mysql.log
general_log       = 1
log_error         = /logs/mysql_safe.log
log_slow_queries  = /logs/mysql-slow.log
long_query_time   = 2
slave-skip-errors = 1062
log-queries-not-using-indexes

server-id         = 1234567
log_bin           = /logs/mysql-bin.log
expire_logs_days  = 2
max_binlog_size   = 100M
```

**Note**: The configuration file will contain a lot more entries but the ones above are the ones you need to pay attention to.

* `bind-address`: We need to comment this line so that we can connect to the instance from somewhere else in the network. Keep this line if you are going to work only on the slave machine and allow no connections from elsewhere. 
* `general_log_file`: The location of your query log file. You can disable this (see next entry) but it is always good to keep it on at least at the start, to ensure that replication is moving smoothly. Tailing that log will give you a nice indicator of the activity in your database.
* `general_log`: Enable or disable the general log
* `log_error`: Where to store the errors log
* `log_slow_queries`: Where to store the slow queries log. Especially helpful in identifying bottlenecks in your application
* `long_query_time`: Time to specify what a slow query is 
* `slave-skip-errors`: 1062 is the *"1062 | Error 'Duplicate entry 'xyz' for key 1' on query. Default database: 'db'. Query: 'INSERT INTO ...'"* error. Helpful especially when the replication starts.
* `log-queries-not-using-indexes`: We want this because it can help identifying potential bottlenecks in the application 
* `server-id`: A unique ID for your slave instance.
* `log_bin`: Where the binary replication logs are kept
* `expire_logs_days`: How long to keep the replication logs for
* `max_binlog_size`: Maximum replication log size (per file)


Once you set these up, restart your MySQL instance

```sh
/etc/init.d/mysql restart
```

##### Download the SSH Public Key for RDS

In your slave server, navigate to `/etc/mysql` and download the `rds-combined-ca-bundle.pem` file. This file will be used by the slave to ensure that all the replication traffic is done using SSL and nobody can eavesdrop on your data in transit.

```sh
cd /etc/mysql
wget http://s3.amazonaws.com/rds-downloads/rds-combined-ca-bundle.pem
```

**NOTE** You can put the `rds-combined-ca-bundle.pem` anywhere on your slave. If you change the path, you will have to modify the command to connect the slave to the master (shown further below) to specify the exact location of the key.

##### Import timezone data

This step might not be necessary depending on your MySQL installation. However since RDS works with UTC, you might find your replication breaking because your slave MySQL instance cannot understand the UTC timezone. The shell command you need to run on your slave machine to fix this is:

```sh
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
```

##### Creating the RDS related tables

RDS uses its own tables to keep track of the replication status and other related data such as the replication heartbeat, configuration etc. Those tables need to be present in the `mysql` database of your slave in order for the replication to work. 

```sql
DROP TABLE IF EXISTS `rds_configuration`;
CREATE TABLE `rds_configuration` (
  `name` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `description` varchar(300) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `rds_global_status_history`;
CREATE TABLE `rds_global_status_history` (
  `collection_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `collection_start` timestamp NULL DEFAULT NULL,
  `variable_name` varchar(64) NOT NULL,
  `variable_value` varchar(1024) NOT NULL,
  `variable_delta` int(20) NOT NULL,
  PRIMARY KEY (`collection_end`,`variable_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `rds_global_status_history_old`;
CREATE TABLE `rds_global_status_history_old` (
  `collection_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `collection_start` timestamp NULL DEFAULT NULL,
  `variable_name` varchar(64) NOT NULL,
  `variable_value` varchar(1024) NOT NULL,
  `variable_delta` int(20) NOT NULL,
  PRIMARY KEY (`collection_end`,`variable_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `rds_heartbeat2`;
CREATE TABLE `rds_heartbeat2` (
  `id` int(11) NOT NULL,
  `value` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `rds_history`;
CREATE TABLE `rds_history` (
  `action_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `called_by_user` varchar(50) NOT NULL,
  `action` varchar(20) NOT NULL,
  `mysql_version` varchar(50) NOT NULL,
  `master_host` varchar(255) DEFAULT NULL,
  `master_port` int(11) DEFAULT NULL,
  `master_user` varchar(16) DEFAULT NULL,
  `master_log_file` varchar(50) DEFAULT NULL,
  `master_log_pos` mediumtext,
  `master_ssl` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `rds_replication_status`;
CREATE TABLE `rds_replication_status` (
  `action_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `called_by_user` varchar(50) NOT NULL,
  `action` varchar(20) NOT NULL,
  `mysql_version` varchar(50) NOT NULL,
  `master_host` varchar(255) DEFAULT NULL,
  `master_port` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `rds_sysinfo`;
CREATE TABLE `rds_sysinfo` (
  `name` varchar(25) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

**NOTE** I am not 100% that all of these tables are needed. I have seen only the `rds_heartbeat2` and `rds_replication_status` used. You can experiment with these when you enable replication and add each table in turn if needed. You can confirm whether the above are correct for your instance by connecting to the master and taking a `mysqldump` of the `mysql` database.

#### Replication

##### Replication user

We need to create a user in our master database that will have the appropriate rights to perform all the replication related actions. We need these commands to be run on the master. For this example I am creating a user called `rpluser` with the password `424242`:

```sql
MySQL [(none)]> CREATE USER 'rpluser'@'%' IDENTIFIED BY '424242';
MySQL [(none)]> GRANT REPLICATION SLAVE ON *.* TO 'rpluser'@'%';
```

##### Master Status

Connect to your master and issue this command:

```sql
MySQL [(none)]> show master status;
```

The output will be something like this:

```sh
+----------------------------+----------+--------------+------------------+-------------------+
| File                       | Position | Binlog_Do_DB | Binlog_Ignore_DB | Executed_Gtid_Set |
+----------------------------+----------+--------------+------------------+-------------------+
| mysql-bin-changelog.000123 |   171819 |              |                  |                   |
+----------------------------+----------+--------------+------------------+-------------------+
```

Keep those values handy (`File` and `Position`) since we will use them to instruct the slave where to start requesting data from the master (binlog file and position).

##### `mysqldump`

Take a database dump of all the databases in RDS (exclude `information_schema` and `mysql`). If your database can afford a bit of downtime you can use the `--opt` flag in `mysqldump`, which will lock all tables until the backup completes. If not, you can use the `--skip-add-locks` flag. More information about `mysqldump` options can be found [here](https://dev.mysql.com/doc/refman/5.5/en/mysqldump.html)

```sh
mysqldump --host=192.168.1.2 --user='root' --password my_db > /backups/my_db.sql
```

Adjust the above command to fit your needs. Once all databases have been dumped, we need to import them in the slave.

##### Importing data in the slave

Navigate to the folder you have all the `*.sql` dump files, connect to the slave database and start sourcing them.

```sh
cd /backups
mysql --host=192.168.1.2 --user='root' --password
MySQL [(none)]> create database my_db;
MySQL [(none)]> use my_db;
MySQL [my_db]> source my_db.sql;
```
Repeat the process of creating the database, using it and sourcing the dump file until all your databases have been imported.

**NOTE** There are other ways of doing the above, piping the results directly to the database or even using RDS to get the data straight from it without a `mysqldump`. Whichever way you choose is up to you. In my experience, the direct import worked for a bit until our database grew to a point that it was timing out or breaking while importing, so I opted for the multi step approach. Have a look at [this section](http://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/MySQL.Procedural.Importing.html) in the AWS RDS documentation for more options.

##### Connecting to the master

Once your restore has been completed it is time to connect our slave to the master. In order to connect to the master from the slave we need to verify the following:

* The name of the RDS instance (for the command below I will use `myinstance.rds.amazonaws.com`)
* The name of the replication user (we chose `rpluser`)
* The password of the replication user (we chose `424242`)
* The master log file (see above, we got `mysql-bin-changelog.000123` from `show master status;`)
* The master log file position (see above, we got `171819`)
* The location of the SSL certificate (we used `/etc/mysql/rds-combined-ca-bundle.pem`)

The command we need to run on the slave MySQL server is (newlines added for readability):

```sql
MySQL [(none)]> CHANGE MASTER TO 
    -> MASTER_HOST='myinstance.rds.amazonaws.com', 
    -> MASTER_USER='rpluser', 
    -> MASTER_PASSWORD='424242', 
    -> MASTER_LOG_FILE='mysql-bin-changelog.000123', 
    -> MASTER_LOG_POS=171819, 
    -> MASTER_SSL=1, 
    -> MASTER_SSL_CERT='', 
    -> MASTER_SSL_CA='/etc/mysql/rds-combined-ca-bundle.pem', 
    -> MASTER_SSL_KEY='';
```

##### Starting the replication

All we have to do now is to start the slave:

```sql
MySQL [(none)]> START SLAVE; 
```

We can check if everything is OK either by using the general log (see `my.cnf` section) by tailing it from the shell:

```sh
tail -f /logs/mysql.log
```

or by issuing this command on the mysql prompt:

```sql
MySQL [(none)]> SHOW SLAVE STATUS \G;
*************************** 1. row ***************************
               Slave_IO_State: Waiting for master to send event
                  Master_Host: myinstance.rds.amazonaws.com
                  Master_User: rpluser
                  Master_Port: 3306
                Connect_Retry: 60
              Master_Log_File: mysql-bin-changelog.000123
          Read_Master_Log_Pos: 171819
               Relay_Log_File: mysqld-relay-bin.000002
                Relay_Log_Pos: 123
...
                   Last_Errno: 0
                   Last_Error: 
...
           Master_SSL_Allowed: Yes
           Master_SSL_CA_File: /etc/mysql/rds-combined-ca-bundle.pem
           Master_SSL_CA_Path: 
              Master_SSL_Cert: 
            Master_SSL_Cipher: 
               Master_SSL_Key: 
...
      Slave_SQL_Running_State: Slave has read all relay log; waiting for the slave I/O thread to update it
...
```

Congratulations on your working RDS slave (offsite) machine :)
 
#### Conclusion

This blog post is by no means exhausting all the topics that replication can cover. For additional information please see the references below.

I hope you find this post helpful :)

#### References

* [RDS Using SSL with a MySQL DB Instance](http://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/CHAP_MySQL.html#MySQL.Concepts.SSLSupport)
* [RDS SSL Public Key](http://s3.amazonaws.com/rds-downloads/rds-combined-ca-bundle.pem)
* [RDS Importing/Exporting Data](http://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/MySQL.Procedural.Importing.html)
* [MySQL Time Zone Support](http://dev.mysql.com/doc/refman/5.5/en/time-zone-support.html)
* [mysqldump](https://dev.mysql.com/doc/refman/5.5/en/mysqldump.html)
