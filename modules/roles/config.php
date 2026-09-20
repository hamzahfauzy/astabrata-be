<?php

use Libraries\Request;
use Libraries\Database as DB;
use Libraries\Response;

return [
    'data' => [
        'table' => 'roles'
    ],
    
    'actions' => [
        'store' => function(Request $request){
            $config = $request->otherData('crudConfig');
            
            $payload = $request->body();
            $permissions = [];
            if(isset($payload['permissions']))
            {
                $permissions = $payload['permissions'];
                unset($payload['permissions']);
            }

            $id = DB::table($config['table'])->insert($payload);

            foreach($permissions as $permission)
            {
                DB::table('role_permissions')->insert([
                    'role_id' => $id,
                    'permission_id' => $permission
                ]);
            }

            
            $data = DB::table($config['table'])->where('id', $id)->first();

            return Response::json(__('create data success'), $data);
        },

        'update' => function(Request $request)
        {
            $config = $request->otherData('crudConfig');
            $id = $request->params('id');
            
            $payload = $request->body();

            DB::table('role_permissions')->where('role_id', $id)->delete();
            if(isset($payload['permissions']))
            {
                $permissions = $payload['permissions'];
                unset($payload['permissions']);
                foreach($permissions as $permission)
                {
                    DB::table('role_permissions')->insert([
                        'role_id' => $id,
                        'permission_id' => $permission
                    ]);
                }
            }
            
            DB::table($config['table'])->where('id', $id)->update($payload);
            $data = DB::table($config['table'])->where('id', $id)->first();

            return Response::json(__('update data success'), $data);
        },
        'show' => function(Request $request)
        {
            $config = $request->otherData('crudConfig');
            $id = $request->params('id');
            $data = DB::table($config['table'])->select($config['view'])->where('id', $id)->first();

            $permission_data = DB::table('role_permissions')
                ->select('permissions.name, permissions.id')
                ->leftJoin('permissions','permissions.id','=','role_permissions.permission_id')
                ->where('role_permissions.role_id', $id)
                ->get();

            $data->permissions = array_map(function($p){ return $p->id; }, $permission_data);
            $data->permission_data = $permission_data;

            return Response::json(__('data retrieved'), $data);
        }
        // index, store, update, destroy, show
    ]
];