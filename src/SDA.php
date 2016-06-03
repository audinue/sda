<?php

namespace audinue;

use \PDO;
use \Exception;

class SDA {

    private $dsn;
    private $username;
    private $password;
    private $options;
    private $pdo;

    function __construct($dsn, $username = null, $password = null, $options = null) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    function dsn() {
        return $this->dsn;
    }

    function username() {
        return $this->username;
    }

    function password() {
        return $this->password;
    }

    function options() {
        return $this->options;
    }

    function pdo() {
        if(!$this->pdo) {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password,
                    $this->options);
        }
        return $this->pdo;
    }

    function execute($sql, $args = null) {
        $statement = $this->pdo()->prepare($sql);
        $error = $this->pdo()->errorInfo();
        if($error[0] != '00000') {
            throw new Exception($error[2]);
        }
        if(func_num_args() == 2) {
            if(!is_array($args)) {
                $args = array($args);
            }
        } else {
            $args = func_get_args();
            array_shift($args);
        }
        $statement->execute($args);
        $error = $statement->errorInfo();
        if($error[0] != '00000') {
            throw new Exception($error[2]);
        }
        return $statement;
    }

    function exec($sql, $args = null) {
        call_user_func_array(array($this, 'execute'), func_get_args());
        return $this;
    }

    function rows($sql, $args = null) {
        return call_user_func_array(array($this, 'execute'), func_get_args())->
                fetchAll(PDO::FETCH_OBJ);
    }

    function row($sql, $args = null) {
        return call_user_func_array(array($this, 'execute'), func_get_args())->
                fetch(PDO::FETCH_OBJ);
    }

    function cell($sql, $args = null) {
        return call_user_func_array(array($this, 'execute'), func_get_args())->
                fetchColumn();
    }

    function column($sql, $args = null) {
        $columns = array();
        foreach(call_user_func_array(array($this, 'execute'), func_get_args())->
                fetchAll(PDO::FETCH_OBJ) as $row) {
            $columns []= current($row);
        }
        return $columns;
    }

    function insert($table, $row) {
        $sql = 'INSERT INTO ' . $table . ' (' .
                implode(', ', array_keys($row)) . ') VALUES (' .
                implode(', ', array_fill(0, count($row), '?')) . ');';
        return $this->exec($sql, array_values($row));
    }

    function update($table, $row, $keys) {
        $sql = 'UPDATE ' . $table . ' SET ' .
                implode(' = ?, ', array_keys($row)) . ' = ? WHERE ' .
                implode(' = ?, ', array_keys($keys)) . ' = ?';
        return $this->exec($sql, array_merge(array_values($row),
                array_values($keys)));
    }

    function delete($table, $keys) {
        $sql = 'DELETE FROM ' . $table . ' WHERE ' .
                implode(' = ?, ', array_keys($keys)) . ' = ?';
        return $this->exec($sql, array_values($keys));
    }

    function id() {
        return $this->pdo()->lastInsertId();
    }

    function begin() {
        $this->pdo()->beginTransaction();
        return $this;
    }

    function commit() {
        $this->pdo()->commit();
        return $this;
    }

    function rollBack() {
        $this->pdo()->rollBack();
        return $this;
    }
}
