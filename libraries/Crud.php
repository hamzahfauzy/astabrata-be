<?php

namespace Libraries;

use Libraries\Services\DatabaseService;
use Libraries\Database as DB;

class Crud {
    
    public function index(Request $request)
    {
        $config = $request->otherData('crudConfig');
        $query = DB::table($config['table'])->select($config['list']);

        $lists = (new DatabaseService)->listing($query, $config['searchable'], $config['filterable'], $config['sortable']);

        return [
            'message' => __('data retrieved'),
            ...$lists,
        ];
    }
    
    public function store(Request $request)
    {
        $config = $request->otherData('crudConfig');
        $id = DB::table($config['table'])->insert($request->body());
        $data = DB::table($config['table'])->where('id', $id)->first();

        return Response::json(__('create data success'), $data);
    }

    public function update(Request $request)
    {
        $config = $request->otherData('crudConfig');
        $id = $request->params('id');
        DB::table($config['table'])->where('id', $id)->update($request->body());

        $data = DB::table($config['table'])->where('id', $id)->first();

        return Response::json(__('update data success'), $data);
    }
    
    public function show(Request $request)
    {
        $config = $request->otherData('crudConfig');
        $id = $request->params('id');
        $data = DB::table($config['table'])->select($config['view'])->where('id', $id)->first();

        return Response::json(__('data retrieved'), $data);
    }

    public function destroy(Request $request)
    {
        $config = $request->otherData('crudConfig');
        $id = $request->params('id');
        DB::table($config['table'])->where('id', $id)->delete();

        return Response::json(__('data deleted'), []);
    }
}