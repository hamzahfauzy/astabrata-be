<?php

use Libraries\Response;
use Libraries\Database as DB;

$data = request()->body();
$activePeriod = request()->otherData('activePeriod');
$profileId = request()->params('id');

$profilePeriod = DB::table('profile_periods')->where('profile_id','=',$profileId)->where('period_id','=',$activePeriod->id)->first();

$check = DB::table('profile_stages')
    ->where('profile_period_id', '=', $profilePeriod->id)
    ->where('name', '=', $profilePeriod->stage)
    ->first();

if(!$check)
{
    $stageData = [
        'profile_period_id' => $profilePeriod->id,
        'name' => 'stage_4',
        'result' => 'process',
        'data' => json_encode([])
    ];
    
    DB::table('profile_stages')->insert($stageData);
}


return Response::json('stage process', []);