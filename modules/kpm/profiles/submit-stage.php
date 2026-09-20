<?php

use Libraries\Response;
use Libraries\Database as DB;

$data = request()->body();
$activePeriod = request()->otherData('activePeriod');
$profileId = request()->params('id');

$profilePeriod = DB::table('profile_periods')->where('profile_id','=',$profileId)->where('period_id','=',$activePeriod->id)->first();

DB::table('profile_stages')->insert([
    'profile_period_id' => $profilePeriod->id,
    'name' => $profilePeriod->stage,
    'result' => $data['results'],
    'data' => json_encode($data)
]);

$id = explode('_', $profilePeriod->stage);

DB::table('profile_periods')->where('id', '=', $profilePeriod->id)
    ->update([
        'stage' => 'stage_' . ($id[1]+1),
        'last_stage' => $profilePeriod->stage,
        'last_stage_result' => $data['results']
    ]);

return Response::json('stage submitted', []);