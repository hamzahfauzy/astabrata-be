<?php

use Libraries\Database as DB;
use Libraries\Response;

$data = DB::table('regions')->select('regions.*')->get();

return Response::json(
    __('regions data retrieved'),
    $data
);