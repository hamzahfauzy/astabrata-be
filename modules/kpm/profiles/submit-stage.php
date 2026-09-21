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

$stageData = [
    'profile_period_id' => $profilePeriod->id,
    'name' => $profilePeriod->stage,
    'result' => $data['results'],
    'data' => json_encode($data)
];

if(!$check)
{
    DB::table('profile_stages')->insert($stageData);
}
else
{
    DB::table('profile_stages')->where('id', '=', $check->id)->update($stageData);
}


$id = explode('_', $profilePeriod->stage);
$nextStage = 'stage_' . ($id[1]+1);

$stageUpdate = [
    'stage' => $nextStage,
    'last_stage' => $profilePeriod->stage,
    'last_stage_result' => $data['results']
];

if($profilePeriod->stage == 'stage_1' && in_array($data['results'],['Jadwalkan Kunjungan','Belum Sesuai']))
{
    unset($stageUpdate['stage']);
    $stageUpdate['status'] = $data['results'];
}
else if($profilePeriod->stage == 'stage_2' && $data['results'] != 'Diusulkan')
{
    unset($stageUpdate['stage']);
    $stageUpdate['status'] = $data['results'];
}

DB::table('profile_periods')->where('id', '=', $profilePeriod->id)
    ->update($stageUpdate);

return Response::json('stage submitted', []);