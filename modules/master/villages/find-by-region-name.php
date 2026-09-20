<?php

use Libraries\Database as DB;
use Libraries\Response;

$name = request()->params('name');
$data = DB::table('villages')->select('villages.*')->leftJoin('regions','regions.id','=','villages.region_id')->where('regions.name','=',$name)->get();

return Response::json(
    __('villages data retrieved'),
    $data
);