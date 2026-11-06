<?php

namespace App\Master;
use Libraries\Database as DB;
use Libraries\Response;

class VillageController {

    function findByRegionId(){
        $id = request()->params('id');
        $data = DB::table('villages')->select('villages.*')->where('region_id','=',$id)->get();

        return Response::json(
            __('villages data retrieved'),
            $data
        );
    }
    
    function findByRegionName(){
        $name = request()->params('name');
        $data = DB::table('villages')->select('villages.*')->leftJoin('regions','regions.id','=','villages.region_id')->where('regions.name','=',$name)->get();

        return Response::json(
            __('villages data retrieved'),
            $data
        );
    }

}