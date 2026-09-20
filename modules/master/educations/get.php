<?php

use Libraries\Database as DB;
use Libraries\Response;

$data = DB::table('educations')->select('educations.*')->get();

return Response::json(
    __('educations data retrieved'),
    $data
);