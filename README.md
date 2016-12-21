[![Build Status](https://travis-ci.org/DevExpress/DevExtreme-PHP-Data.svg?branch=master)](https://travis-ci.org/DevExpress/DevExtreme-PHP-Data)
# DevExtreme PHP Data
---

This library is intended for loading data from a MySQL (__[mysqlnd](http://php.net/manual/en/book.mysqlnd.php) is required__) database, based on expressions passed from a client by a DevExtreme data source. The library will allow you to implement a data service that supports
the protocol described in the following help topics:

[dxDataGrid - Use CustomStore](https://js.devexpress.com/Documentation/16_2/Guide/Widgets/DataGrid/Use_CustomStore/)

[dxPivotGrid - Use CustomStore](https://js.devexpress.com/Documentation/16_2/Guide/Widgets/PivotGrid/Use_CustomStore/)

Moreover, the library will allow you to perform all operations such as, [filtering](https://js.devexpress.com/Documentation/16_2/Guide/Data_Layer/Data_Layer/#Data_Layer_Data_Layer_Reading_Data_Filtering),
[sorting](https://js.devexpress.com/Documentation/16_2/Guide/Data_Layer/Data_Layer/#Data_Layer_Data_Layer_Reading_Data_Sorting), [paging](https://js.devexpress.com/Documentation/16_2/Guide/Data_Layer/Data_Layer/#Data_Layer_Data_Layer_Reading_Data_Paging) and [grouping](https://js.devexpress.com/Documentation/16_2/Guide/Data_Layer/Data_Layer/#Data_Layer_Data_Layer_Reading_Data_Grouping) on the server side.



## Getting Started
---

If you wish to load data from a MySQL data table based on parameters received from the client side, perform the following steps.

* Copy the __DevExtreme__ folder to your project.
* Include the __LoadHelper.php__ file to your PHP script where you wish to call the __DevExtreme PHP Data__ library API.
* Register the library autoloader by executing the following code:

```PHP
spl_autoload_register(array("DevExtreme\LoadHelper", "LoadModule"));
```

Since all classes in the library are wrapped in the DevExtreme namespace, use the [use](http://php.net/manual/en/language.namespaces.importing.php)
operator to get rid of long names in your code. Only two classes are required: ```DbSet``` and ```DataSourceLoader```. Execute the following code to be ready to use these classes:

```PHP
use DevExtreme\DbSet;
use DevExtreme\DataSourceLoader;
```

To load data, perform the following steps.

* Create the [mysqli](http://php.net/manual/en/book.mysqli.php) instance passing your MySQL server user credentials to its constructor.
* Create the ```DbSet``` instance passing the ```mysqli``` instance as a first parameter and a table name from which you wish to load data as a second parameter
to the ```DbSet``` constructor.
* Get parameters received from the client side and pass them with the ```DbSet``` instance to the ```Load``` method of the ```DataSourceLoader``` class. This
is a static method and you will not need to create an instance of the class. The following code snippet shows how to do this:

```PHP
$mySQL = new mysqli("serverName", "userName", "password", "databaseName");
$dbSet = new DbSet($mySQL, "tableName");
$result = DataSourceLoader::Load($dbSet, $params);
```
Note that parameters (```$params```) must be passed as an associative [array](http://php.net/manual/en/language.types.array.php).

That's it. The ```$result``` variable will contain the required results. It will represent an array of items. If you wish to send it to the client to be
dislplayed in a DevExtreme widget, convert it to the [JSON](https://en.wikipedia.org/wiki/JSON) format. You can use the [json_encode](http://php.net/manual/en/function.json-encode.php) function
for this task.

The __example__ folder of this repository contains an example that shows how to use the library. To test it:
* copy this folder to your web server folder;
* execute the following command in the __example__ folder;
```
bower install
```
* Specify user credentials, database name and table name in the _php/DataController.php_ file (see the ```DataController``` constructor) and open the _index.html_ file in a web browser.



## Diving deeper
---

The ```DbSet``` class has the following public methods that you can use without using the ```DataSourceLoader``` class.
* ```Delete``` - deletes an item from a data table.
It accepts a single parameter that represents a key. The key must be passed as an associative array. For example, if your key is ID and its value is 1, it should be passed to the method in the following manner:

```PHP
$dbSet->Delete(array("ID" => 1));
```
The method returns a number of the affected rows.


* ```GetLastError``` - returns a string representation of the error that was thrown by the last executed SQL query.
The method does not accept parameters.


* ```Insert``` - insert a new row to your data table.
It accepts a single parameter that represents an associative array of items.
The method returns a number of the affected rows.


* ```Update``` - updates a certain item in your data table that is identified by a key value.
It accepts two parameters. The first parameter represents a key value. It should be specified in the same manner as a key value for the ```Delete``` method.
The second parameter represents an associative array of values that corresponds to data fields you wish to update.
The method returns a number of the affected rows.
