<?php

namespace App\Master;

use Libraries\Database as DB;
use Libraries\Response;

class RegionController {

    public function get(){
        $data = DB::table('regions')->select('regions.*')->get();

        return Response::json(
            __('regions data retrieved'),
            $data
        );
    }
}