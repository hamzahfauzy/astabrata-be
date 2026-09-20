<?php

namespace Libraries\Services;

use Libraries\Database;
use Libraries\Request;

class DatabaseService
{
    protected Request $request;

    public function __construct()
    {
        $this->request = request();
    }

    /**
     * Handle search, filter, sorting & pagination.
     */
    public function listing(
        Database $query,
        array $searchable = [],
        array $filterable = [],
        array $sortable = [],
        string $defaultSort = 'id',
        string $defaultOrder = 'DESC'
    ): array {

        $this->applySearch($query, $searchable);

        $this->applyFilter($query, $filterable);

        $this->applySort(
            $query,
            $sortable,
            $defaultSort,
            $defaultOrder
        );

        return $this->paginate($query);
    }

    protected function applySearch(Database $query, array $columns): void
    {
        $keyword = trim($this->request->query('search', ''));

        if ($keyword === '') {
            return;
        }

        $query->search($columns, $keyword);
    }

    protected function applyFilter(Database $query, array $columns): void
    {
        foreach ($columns as $column) {

            $value = $this->request->query($column);

            if ($value === null || $value === '') {
                continue;
            }

            $query->where($column, $value);

        }
    }

    protected function applySort(
        Database $query,
        array $sortable,
        string $defaultSort,
        string $defaultOrder
    ): void {

        $sort = $this->request->query('sort', $defaultSort);

        $order = strtoupper(
            $this->request->query('order', $defaultOrder)
        );

        if (!empty($sortable) && !in_array($sort, $sortable)) {
            $sort = $defaultSort;
        }

        $order = $order === 'ASC'
            ? 'ASC'
            : 'DESC';

        $query->orderBy($sort, $order);
    }

    protected function paginate(Database $query): array
    {
        $page = max(
            1,
            (int)$this->request->query('page', 1)
        );

        $perPage = max(
            1,
            (int)$this->request->query('per_page', 10)
        );

        $countQuery = clone $query;

        $total = $countQuery->count();

        $query->limit($perPage, ($page - 1) * $perPage);

        return [
            'data' => $query->get(),

            'meta' => [

                'page' => $page,

                'per_page' => $perPage,

                'total' => $total,

                'last_page' => (int)ceil($total / $perPage),

                'from' => $total
                    ? (($page - 1) * $perPage) + 1
                    : 0,

                'to' => min(
                    $page * $perPage,
                    $total
                )
            ]
        ];
    }
}