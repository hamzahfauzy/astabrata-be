<?php

namespace App\Master;

use Libraries\Request;
use Libraries\Database as DB;
use Libraries\Response;
use Libraries\Services\DatabaseService;

class AssignmentController {

    public static function config(){
        return [
            'data' => [
                'table' => 'user_assignment'
            ],
            
            'actions' => [
                'index' => function(Request $request){
                    return self::index($request);
                },
                'store' => function(Request $request){
                    return self::store($request);
                },
                'update' => function(Request $request){
                    return self::update($request);
                },
                'show' => function(Request $request){
                    return self::show($request);
                }
                // index, store, update, destroy, show
            ]
        ];
    }

    static function index(Request $request){

        $config = $request->otherData('crudConfig');
        $query = self::query();

        $lists = (new DatabaseService)->listing($query, $config['searchable'], $config['filterable'], $config['sortable']);

        return [
            'message' => __('data retrieved'),
            ...$lists,
        ];
    }

    static function store(Request $request){
        $payload = $request->body();
        $payload['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);

        $assignmentPayload = [
            'model_id' => $payload['model_id'],
            'model_name' => 'instances'
        ];

        unset($payload['model_id']);

        $id = DB::table('users')->insert($payload);
        $assignmentPayload['user_id'] = $id;
        DB::table('user_assignment')->insert($assignmentPayload);

        $defaultRoleId = env('DEFAULT_ASSIGNMENT_ROLE_ID', 0);
        if($defaultRoleId)
        {
            DB::table('user_roles')->insert([
                'role_id' => $defaultRoleId,
                'user_id' => $id
            ]);
        }

        $data = self::query()->where('user_assignment.id', $id)->first();

        return Response::json(__('create data success'), $data);
    }

    static function update(Request $request){
        $payload = $request->body();

        if(isset($payload['password']) && !empty($payload['password']))
        {
            $payload['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);
        }
        else
        {
            unset($payload['password']);
        }

        $assignmentPayload = [
            'model_id' => $payload['model_id'],
            'model_name' => 'instances'
        ];

        unset($payload['model_id']);

        $id = $request->params('id');
        $data = self::query()->where('user_assignment.id', $id)->first();
        
        DB::table('users')->where('id',$data->user_id)->update($payload);
        DB::table('user_assignment')->where('id', $id)->update($assignmentPayload);

        $data = self::query()->where('user_assignment.id', $id)->first();

        return Response::json(__('update data success'), $data);
    }

    static function show(Request $request)
    {
        $id = $request->params('id');
        $data = self::query()->where('user_assignment.id', $id)->first();

        return Response::json(__('data retrieved'), $data);
    }

    static function query(){
        return DB::table('user_assignment')
                ->select('user_assignment.*, users.name, users.email, users.username, users.is_active, instances.name instance_name, instances.region_type, CASE WHEN instances.region_type = 0 THEN "Kabupaten Asahan" ELSE instances.region_type END region, regions.name region_name, villages.name village_name')
                ->leftJoin('users','users.id','=','user_assignment.user_id')
                ->leftJoin('instances','instances.id','=','user_assignment.model_id AND user_assignment.model_name = "instances"')
                ->leftJoin('regions','regions.id','=','instances.region_id')
                ->leftJoin('villages','villages.id','=','instances.village_id');
    }

}