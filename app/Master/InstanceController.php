<?php

namespace App\Master;

use Libraries\Request;
use Libraries\Database as DB;
use Libraries\Services\DatabaseService;

class InstanceController {

    public static function config(){
        return [
            'data' => [
                'table' => 'instances'
            ],
            
            'actions' => [
                'index' => function(Request $request){
                    return self::index($request);
                }
                // index, store, update, destroy, show
            ]
        ];
    }

    static function index(Request $request){

        $config = $request->otherData('crudConfig');
        $query = DB::table($config['table'])
                ->select('instances.*, CASE WHEN region_type = 0 THEN "Kabupaten Asahan" ELSE region_type END region, regions.name region_name, villages.name village_name')
                ->leftJoin('regions','regions.id','=','instances.region_id')
                ->leftJoin('villages','villages.id','=','instances.village_id');

        $lists = (new DatabaseService)->listing($query, $config['searchable'], $config['filterable'], $config['sortable']);

        return [
            'message' => __('data retrieved'),
            ...$lists,
        ];
    }

    function get(Request $request){
        $query = DB::table('instances')
                ->select('
                instances.*, 
                CASE WHEN region_type = 0 THEN "Kabupaten Asahan" ELSE region_type END region, 
                CASE WHEN region_type = 0 THEN instances.name ELSE CONCAT(regions.name, " - ",instances.name) END name, 
                regions.name region_name, 
                villages.name village_name')
                ->leftJoin('regions','regions.id','=','instances.region_id')
                ->leftJoin('villages','villages.id','=','instances.village_id');

        $lists = (new DatabaseService)->listing($query);

        return [
            'message' => __('data retrieved'),
            ...$lists,
        ];
    }

}