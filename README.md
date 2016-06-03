# SDA

Simple Data Access for PHP.

## Install

<pre><code>composer require <b>audinue/sda</b></code></pre>

## Examples

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use audinue\SDA;

$sda = new SDA('sqlite::memory:');

// Begin a transaction
$sda->begin();

// Execute a statement
$sda->exec('CREATE TABLE users (id PRIMARY KEY, password)');

// Ways to pass input parameters
$sda->exec('INSERT INTO users VALUES (?, ?)', 'foo', 'bar');
$sda->exec('INSERT INTO users VALUES (?, ?)', ['baz', 'qux']);
$sda->exec('INSERT INTO users VALUES (:id, :password)', [
	'id' => 'quux',
	'password' => 'zaa'
]);

// Get the last inserted ID
echo $sda->id() . PHP_EOL;

// Get rows
foreach($sda->rows('SELECT * FROM users') as $row) {
	var_dump($row);
}

// Get a row
var_dump($sda->row('SELECT * FROM users WHERE id = ? LIMIT 1', 'bar'));

// Get a cell
echo $sda->cell('SELECT COUNT(*) FROM users') . PHP_EOL;

// Get a column
var_dump($sda->column('SELECT id FROM users'));

// Commit current transaction
$sda->commit();

// Begin another transaction
$sda->begin();

// Another way to inserting row
// INSERT INTO users (id, password) VALUES ('laa', 'laaa')
$sda->insert('users', [
	'id' => 'laa',
	'password' => 'laaa'
]);

// Another way to update a row
// UPDATE users SET password = 'foo' WHERE id = 'foo'
$sda->update('users', [
	'password' => 'foo'
], [
	'id' => 'foo'
]);

// And another way to delete a row
$sda->delete('users', [
	'id' => 'laa'
]);

// Rollback current transaction
$sda->rollback();

// Get the PDO
$pdo = $sda->pdo();
```

## License

[MIT](./LICENSE) &copy; [Audi Nugraha](https://facebook.com/audinue)