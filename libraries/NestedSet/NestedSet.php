<?php

namespace Libraries\NestedSet;

use PDO;
use Exception;

class NestedSet
{
    protected PDO $db;
    protected string $table;

    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * Membuat root node
     */
    public function createRoot(array $data): int
    {
        $data['_lft'] = 1;
        $data['_rgt'] = 2;
        $data['depth'] = 0;

        return $this->insert($data);
    }

    /**
     * Menambah child pada parent
     */
    public function appendChild(int $parentId, array $data): int
    {
        $parent = $this->find($parentId);

        if (!$parent) {
            throw new Exception("Parent not found.");
        }

        $left = $parent['_rgt'];

        $this->db->beginTransaction();

        try {

            $this->db->prepare("
                UPDATE {$this->table}
                SET _rgt = _rgt + 2
                WHERE _rgt >= ?
            ")->execute([$left]);

            $this->db->prepare("
                UPDATE {$this->table}
                SET _lft = _lft + 2
                WHERE _lft > ?
            ")->execute([$left]);

            $data['_lft'] = $left;
            $data['_rgt'] = $left + 1;
            $data['depth'] = $parent['depth'] + 1;
            $data['parent_id'] = $parentId;

            $id = $this->insert($data);

            $this->db->commit();

            return $id;

        } catch (\Throwable $e) {

            $this->db->rollBack();

            throw $e;
        }
    }

    /**
     * Ambil node
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Semua children
     */
    public function descendants(int $id): array
    {
        $node = $this->find($id);

        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE _lft > ?
            AND _rgt < ?
            ORDER BY _lft
        ");

        $stmt->execute([
            $node['_lft'],
            $node['_rgt']
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Semua parent
     */
    public function ancestors(int $id): array
    {
        $node = $this->find($id);

        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE _lft < ?
            AND _rgt > ?
            ORDER BY _lft
        ");

        $stmt->execute([
            $node['_lft'],
            $node['_rgt']
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Apakah leaf
     */
    public function isLeaf(int $id): bool
    {
        $node = $this->find($id);

        return ($node['_rgt'] - $node['_lft']) === 1;
    }

    /**
     * Insert helper
     */
    protected function insert(array $data): int
    {
        $columns = implode(',', array_keys($data));
        $values = implode(',', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
            ($columns)
            VALUES ($values)
        ");

        $stmt->execute(array_values($data));

        return (int)$this->db->lastInsertId();
    }
}