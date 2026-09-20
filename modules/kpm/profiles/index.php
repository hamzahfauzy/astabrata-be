<?php

use Libraries\Database as DB;
use Libraries\Services\DatabaseService;

$query = DB::table('profiles')->select('*');

$lists = (new DatabaseService)->listing($query);

return [
    'messages' => __('Data retrieved.'),
    ...$lists

];