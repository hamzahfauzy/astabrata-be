<?php

use Libraries\Database as DB;
use Libraries\Response;

$id = request()->params('id');
$data = DB::table('villages')->select('villages.*')->where('region_id','=',$id)->get();

return Response::json(
    __('villages data retrieved'),
    $data
);