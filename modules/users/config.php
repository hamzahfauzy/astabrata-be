<?php

use Libraries\Request;
use Libraries\Database as DB;
use Libraries\Response;

return [
    'data' => [
        'table' => 'users',
        'searchable' => [
            'name', 'username', 'email'
        ],
        'filterable' => [
            'is_active'
        ],
        'list'  => [
            'id', 'name', 'username', 'email', 'is_active', 'last_login_at', 'created_at', 'updated_at'
        ],
        'view'  => [
            'id', 'name', 'username', 'email', 'is_active', 'last_login_at', 'created_at', 'updated_at'
        ],
    ],
    
    'actions' => [
        'store' => function(Request $request){
            $config = $request->otherData('crudConfig');
            
            $payload = $request->body();
            $payload['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);
            $roles = $payload['roles'];
            unset($payload['roles']);

            $id = DB::table($config['table'])->insert($payload);

            foreach($roles as $role)
            {
                DB::table('user_roles')->insert([
                    'user_id' => $id,
                    'role_id' => $role
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

            if(empty($payload['password']))
            {
                unset($payload['password']);
            }
            else
            {
                $payload['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);
            }

            DB::table('user_roles')->where('user_id', $id)->delete();
            if(isset($payload['roles']))
            {
                $roles = $payload['roles'];
                unset($payload['roles']);
                foreach($roles as $role)
                {
                    DB::table('user_roles')->insert([
                        'user_id' => $id,
                        'role_id' => $role
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

            $role_data = DB::table('user_roles')
                ->select('roles.name, roles.id')
                ->leftJoin('roles','roles.id','=','user_roles.role_id')
                ->where('user_roles.user_id', $id)
                ->get();

            $data->roles = array_map(function($p){ return $p->id; }, $role_data);
            $data->role_data = $role_data;

            return Response::json(__('data retrieved'), $data);
        }
        // 'index' => 'modules/users/index'
        // index, store, update, destroy, show
    ]
];