<?php

use Libraries\Request;
use Libraries\Database as DB;
use Libraries\Services\DatabaseService;

return [
    'data' => [
        'table' => 'villages'
    ],
    
    'actions' => [
        'index' => function(Request $request) {
            $config = $request->otherData('crudConfig');
            $query = DB::table($config['table'])->select('villages.*, regions.name region_name')->leftJoin('regions','regions.id','=','villages.region_id');

            $lists = (new DatabaseService)->listing($query, $config['searchable'], $config['filterable'], $config['sortable']);

            return [
                'message' => __('data retrieved'),
                ...$lists,
            ];
        }
        // index, store, update, destroy, show
    ]
];