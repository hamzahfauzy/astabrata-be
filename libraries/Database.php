<?php

namespace Libraries;

use Exception;
use InvalidArgumentException;
use PDO;

class Database
{
    protected static $pdo;
    protected $table;
    protected $select = '*';
    protected $wheres = [];
    protected $bindings = [];
    protected $joins = [];
    protected $groupBy = '';
    protected $orderBy = '';
    protected $limit = '';
    protected $offset = '';
    protected $type = 'select';
    protected $insertData = [];
    protected $updateData = [];

    // --- AUTO CONNECT ---
    public static function getConnection(): PDO
    {
        if (!self::$pdo) {
            $dbconfig = config('database');
            $host = $dbconfig['host'];
            $dbname = $dbconfig['database'];
            $user = $dbconfig['user'];
            $pass = $dbconfig['password'];
            $port = $dbconfig['port'];

            self::$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$pdo;
    }

    // --- Factory ---
    public static function table($table)
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function select($columns = '*')
    {
        $this->type = 'select';
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    private function __construct() {}

    protected function addWhere(
        string $boolean,
        string $condition,
        array $bindings = []
    ): void
    {
        $prefix = empty($this->wheres)
            ? ''
            : strtoupper($boolean) . ' ';

        $this->wheres[] = $prefix . $condition;

        $this->bindings = array_merge(
            $this->bindings,
            $bindings
        );
    }

    // --- QUERY BUILDER FUNCTIONS ---
    public function where($column, $operator, $value = false)
    {
        if ($value === false) {
            $value = $operator;
            $operator = '=';
        }

        $this->addWhere(
            'AND',
            "$column $operator ?",
            [$value]
        );

        return $this;
    }

    public function orWhere($column, $operator, $value = false)
    {
        if ($value === false) {
            $value = $operator;
            $operator = '=';
        }

        $this->addWhere(
            'OR',
            "$column $operator ?",
            [$value]
        );

        return $this;
    }

    public function whereIn(string $column, array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('whereIn values cannot be empty.');
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $this->addWhere(
            'AND',
            "$column IN ($placeholders)",
            $values
        );

        return $this;
    }

    public function whereNotIn(string $column, array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('whereNotIn values cannot be empty.');
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $this->addWhere(
            'AND',
            "$column NOT IN ($placeholders)",
            $values
        );

        return $this;
    }

    public function orWhereIn($column, array $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $this->addWhere(
            'OR',
            "$column IN ($placeholders)",
            $values
        );

        return $this;
    }
    
    public function orWhereNotIn($column, array $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $this->addWhere(
            'OR',
            "$column NOT IN ($placeholders)",
            $values
        );

        return $this;
    }

    // ✅ JOIN
    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->joins[] = strtoupper($type) . " JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    // ✅ GROUP BY
    public function groupBy($columns)
    {
        $this->groupBy = "GROUP BY " . (is_array($columns) ? implode(', ', $columns) : $columns);
        return $this;
    }

    // ✅ ORDER BY
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    // ✅ LIMIT
    public function limit($limit, $offset = null)
    {
        $this->limit = "LIMIT " . ($offset ? "$offset, $limit" : $limit);
        return $this;
    }

    // ✅ Transaction helpers
    public static function beginTransaction()
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit()
    {
        return self::getConnection()->commit();
    }

    public static function rollBack()
    {
        return self::getConnection()->rollBack();
    }

    // --- EXECUTION ---
    public function get()
    {
        $sql = $this->toSql();
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function exec($query, $bindings = [])
    {
        $stmt = self::getConnection()->prepare($query);
        $stmt->execute($bindings);
        return $stmt;
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function toSql()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' ', $this->wheres);
        }
        if ($this->groupBy) $sql .= " " . $this->groupBy;
        if ($this->orderBy) $sql .= " " . $this->orderBy;
        if ($this->limit) $sql .= " " . $this->limit;
        return $sql;
    }

        // ✅ INSERT
    public function insert(array $data)
    {
        $this->type = 'insert';
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(array_values($data));
        
        return self::lastInsertId();
    }

    public function update(array $data)
    {
        $this->type = 'update';

        // pastikan ada data yang diupdate
        if (empty($data)) {
            throw new Exception("Update data tidak boleh kosong");
        }

        // buat SET column = ?
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET $set";

        // tambahkan WHERE jika ada
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' ', $this->wheres);
        }

        $stmt = self::getConnection()->prepare($sql);

        // binding values = data + where
        $stmt->execute(array_merge(array_values($data), $this->bindings));

        return $stmt->rowCount();
    }


    // ✅ DELETE
    public function delete()
    {
        $this->type = 'delete';
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' ', $this->wheres);
        }

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($this->bindings);
        
        return $stmt->rowCount();
    }

    public static function lastInsertId()
    {
        return self::getConnection()->lastInsertId();
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function count(string $column = '*'): int
    {
        $query = clone $this;

        $query->select("COUNT($column) AS aggregate");

        $query->limit = null;
        $query->offset = null;
        $query->orderBy = [];

        $result = $query->first();

        return (int)$result->aggregate;
    }

    public function search(array $columns, string $keyword): static
    {
        $conditions = [];
        $bindings = [];

        foreach ($columns as $key => $value) {

            if (is_int($key)) {
                // Format: 'name'
                $column = $value;
            } else {
                // Format: 'role_name' => 'roles.name'
                $column = $value;
            }

            $conditions[] = "{$column} LIKE ?";
            $bindings[] = "%{$keyword}%";
        }

        return $this->whereRaw(
            '(' . implode(' OR ', $conditions) . ')',
            $bindings
        );
    }

    public function whereRaw(string $sql, array $bindings = []): static
    {
        $this->addWhere('AND', $sql, $bindings);

        return $this;
    }

}
