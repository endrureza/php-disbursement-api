<?php

namespace App;

use mysqli;

class Connection
{
    protected $connection;

    protected $table;

    public function __construct(
        $host = 'localhost',
        $db = 'disburse',
        $user = 'root',
        $pass = 'root'
    ) {
        if (!extension_loaded('mysqli')) {
            throw new \ErrorException('mysqli library is not loaded');
        }

        $this->connection = new mysqli($host, $user, $pass, $db);
        if ($this->connection->connect_error) {
            $this->error('Failed to connect to MysQL - ' . $this->connection->connect_error);
        }
        $this->connection->set_charset('utf8');
    }

    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * Display all table records
     *
     * @param array $data
     * @return void
     */
    public function get($data = [])
    {
        if (!empty($data)) {
            $data = implode(',', $data);
        } else {
            $data = '*';
        }

        $sql = 'select ' . $data . ' from ' . $this->table . ' where 1';

        $result = $this->connection->query($sql);

        $records = [];

        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }

        return $records;
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return void
     */
    public function create(array $data)
    {
        $columns = "`" . implode("`,`", array_keys($data)) . "`";

        $values = "'" . implode("','", array_values($data)) . "'";

        $sql = "insert into " . $this->table . " (" . $columns . ") values (" . $values . ")";

        if (!$this->connection->query($sql)) {
            $this->error("Error: " . $this->connection->error);
        }

        $lastRecord = $this->find($this->connection->insert_id);

        return $lastRecord;
    }

    /**
     * Get single record by id
     *
     * @param integer $id
     * @param array $data
     * @return void
     */
    public function find(int $id, $data = [])
    {
        if (!empty($data)) {
            $data = implode(',', $data);
        } else {
            $data = '*';
        }

        $sql = 'select ' . $data . ' from ' . $this->table . ' where id=' . $id;

        $result = $this->connection->query($sql);

        $records = $result->fetch_assoc();

        return $records;
    }

    /**
     * Update database records
     *
     * @param array $condition
     * @param array $columns
     * @return void
     */
    public function update(array $conditions, array $columns)
    {
        $tempColumns = [];

        foreach ($columns as $key => $value) {
            $tempColumns[] = "`" . $key . "`='" . $value . "'";
        }

        $columns = implode(" , ", $tempColumns);

        $tempConditions = [];

        foreach ($conditions as $key => $value) {
            $tempConditions[] = "`" . $key . "`='" . $value . "'";
        }

        $conditions = implode(" and ", $tempConditions);

        $sql = 'update ' . $this->table . ' set ' . $columns . ' where ' . $conditions;

        $this->connection->query($sql);

        return true;
    }

    /**
     * Show connection error and exit
     *
     * @param $error
     * @return void
     */
    public function error($error)
    {
        exit($error);
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Create new table
     *
     * @param string $table
     * @param array $columns
     * @return mixed
     */
    public function createTable(string $table, array $columns)
    {
        $tempColumns = [];

        foreach ($columns as $key => $value) {
            $tempColumns[] = $key . " " . $value;
        }

        $columns = implode(",", $tempColumns);

        $sql = "create table " . $table . " (" . $columns . ")";

        $result = $this->connection->query($sql);

        if (!$result) {
            $this->error("Error: ", $this->connection->error);
        }

        return true;
    }

    /**
     * Purge any exist table
     *
     * @return mixed
     */
    public function purgeTable()
    {
        $sql = "show tables";

        $result = $this->connection->query($sql);

        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $this->connection->query('drop table if exists ' . $row[0]);
            }
        }

        return true;
    }
}
