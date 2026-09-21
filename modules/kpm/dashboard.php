<?php

use Libraries\Database as DB;
use Libraries\Response;

$user = auth()->user();
$activePeriod = request()->otherData('activePeriod');
$query = DB::table('profiles')->select('COUNT(profiles.id) total')
        ->leftJoin('profile_periods','profile_periods.profile_id','=','profiles.id')
        ->where('profile_periods.period_id','=',$activePeriod->id);

$profiles = DB::table('profiles')->select('
profiles.id,
profiles.name,
CONCAT(
    LEFT(personal_number, 4),
    "********",
    RIGHT(personal_number, 4)
) AS personal_number,
CONCAT(
    LEFT(family_number, 4),
    "********",
    RIGHT(family_number, 4)
) AS family_number,
profile_periods.village,
profile_periods.region,
profile_periods.stage,
profile_periods.status
')
        ->leftJoin('profile_periods','profile_periods.profile_id','=','profiles.id')
        ->where('profile_periods.period_id','=',$activePeriod->id);
        // ->leftJoin('profile_stages','profile_stages.profile_period_id','=','profile_periods.id')

$data = [];

if(auth()->can('desa'))
{
    $assigment = DB::table('user_assignment')
        ->select('user_assignment.*, villages.name village_name, regions.name region_name')
        ->leftJoin('villages','villages.id','=','user_assignment.model_id')
        ->leftJoin('regions','regions.id','=','villages.region_id')
        ->where('user_id','=',$user->id)->first();

    $query = $query->where('profile_periods.region','=',$assigment->region_name)
        ->where('profile_periods.village','=',$assigment->village_name);

    $data['totalProfileDraft'] = (clone $query)->where('profile_periods.stage','=','stage_1')->where('profile_periods.status','=','Draft')->first()?->total;
    $data['totalProfileWait'] = (clone $query)->where('profile_periods.stage','=','stage_1')->where('profile_periods.status','=','Menunggu Verifikasi')->first()?->total;
    $data['totalProfileRevision'] = (clone $query)->where('profile_periods.stage','=','stage_1')->where('profile_periods.status','=','Revisi')->first()?->total;
    $profiles = $profiles->where('profile_periods.region','=',$assigment->region_name)
        ->where('profile_periods.village','=',$assigment->village_name);
}

if(auth()->can('pendamping'))
{
    // $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_1" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_1")');
}

else if(auth()->can('kecamatan'))
{
    // $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_2" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_2")');
}

$data['totalProfile'] = $query->first()?->total ?? 0;
$data['profiles'] = $profiles->get();

return Response::json(__('Data retrieved.'), $data);