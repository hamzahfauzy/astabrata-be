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
    ->update([
        'result' => 'Diajukan'
    ]);

$id = explode('_', $profilePeriod->stage);
$nextStage = 'stage_' . ($id[1]+1);

$stageUpdate = [
    'stage' => $nextStage,
    'last_stage' => $profilePeriod->stage,
    'last_stage_result' => 'Diajukan',
    'status' => 'Menunggu Verifikasi',
];

DB::table('profile_periods')->where('id', '=', $profilePeriod->id)
    ->update($stageUpdate);

return Response::json('berhasil diajukan', []);