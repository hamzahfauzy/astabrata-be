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
profile_assessments.assessor_name,
profile_periods.village,
profile_periods.region,
profile_periods.stage,
profile_periods.status,
profile_periods.created_at period_created,
stages.name stage_name,
stages.result stage_result,
stages.data stage_data
')
        ->leftJoin('profile_periods','profile_periods.profile_id','=','profiles.id')
        ->leftJoin('profile_assessments','profile_assessments.profile_period_id','=','profile_periods.id')
        ->where('profile_periods.period_id','=',$activePeriod->id);
        // ->leftJoin('profile_stages','profile_stages.profile_period_id','=','profile_periods.id')

$data = [];

$stageDataLeftJoin = "(
    SELECT *
    FROM (
        SELECT
            h.*,
            ROW_NUMBER() OVER (
                PARTITION BY h.profile_period_id
                ORDER BY h.created_at DESC, h.id DESC
            ) AS rn
        FROM profile_stages h
    ) x
    WHERE x.rn = 1
) stages";

if(auth()->can('*'))
{

}

else if(auth()->can('desa'))
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
    $data['totalProfileRevision'] = (clone $query)->where('profile_periods.stage','=','stage_1')->where('profile_periods.status','=','Belum Sesuai')->first()?->total;
    $profiles = $profiles->where('profile_periods.region','=',$assigment->region_name)
        ->where('profile_periods.village','=',$assigment->village_name)->where('profile_periods.stage','=','stage_1');
}

else if(auth()->can('pendamping'))
{

    $data['totalProfileVerified'] = (clone $query)->where('profile_periods.stage','<>','stage_1')->first()?->total;
    $data['totalProfileScheduled'] = (clone $query)->where('profile_periods.stage','=','stage_1')->where('profile_periods.status','=','Jadwalkan Kunjungan')->first()?->total;
    $data['totalProfileWait'] = (clone $query)->where('profile_periods.stage','=','stage_1')->where('profile_periods.status','=','Menunggu Verifikasi')->first()?->total;
    $data['totalProfileReturn'] = (clone $query)->where('profile_periods.stage','=','stage_1')->where('profile_periods.status','=','Belum Sesuai')->first()?->total;
    $profiles = $profiles->where('profile_periods.stage','=','stage_1')->whereRaw('profile_periods.status IN ("Menunggu Verifikasi","Jadwalkan Kunjungan")');
}

else if(auth()->can('kecamatan'))
{
    $data['totalProfileReceived'] = (clone $query)->whereRaw('EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_1" AND result = "Sesuai"  AND profile_period_id = profile_periods.id)')->first()?->total;
    $data['totalProfileReady'] = (clone $query)->where('profile_periods.stage','=','stage_2')->where('profile_periods.status','=','Sesuai')->first()?->total;
    $data['totalProfileNeedToCheck'] = (clone $query)->where('profile_periods.stage','=','stage_2')->where('profile_periods.status','=','Menunggu Verifikasi')->first()?->total;
    $data['totalProfileRevision'] = (clone $query)->where('profile_periods.stage','=','stage_2')->where('profile_periods.status','=','Belum Sesuai')->first()?->total;
    $profiles = $profiles->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_1" AND result = "Sesuai" AND profile_period_id = profile_periods.id))');
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_2" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_2")');

    $stageDataLeftJoin = "(
        SELECT *
        FROM (
            SELECT
                h.*,
                ROW_NUMBER() OVER (
                    PARTITION BY h.profile_period_id
                    ORDER BY h.created_at DESC, h.id DESC
                ) AS rn
            FROM profile_stages h WHERE name IN ('stage_1','stage_2') ORDER BY name DESC
        ) x
        WHERE x.rn = 1
    ) stages";
}

else if(auth()->can('dinsos'))
{
    $data['totalProfileIn'] = (clone $query)->whereRaw('profile_periods.stage NOT IN ("stage_1","stage_2")')->first()?->total;
    $data['totalProfileReady'] = (clone $query)->whereRaw('EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_3" AND profile_stages.result = "Sesuai" AND profile_period_id = profile_periods.id)')->first()?->total;
    $data['totalProfileRevision'] = (clone $query)->whereRaw('EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_3" AND profile_stages.result = "Belum Sesuai" AND profile_period_id = profile_periods.id)')->first()?->total;
    $data['totalTarget'] = DB::table('profile_periods')->exec('SELECT SUM(CASE WHEN remaining = 0 THEN 1 ELSE 0 END) total_target FROM (SELECT region, COUNT(*) AS total, 4 AS target, GREATEST(4 - COUNT(*), 0) AS remaining FROM profile_periods WHERE period_id = ? GROUP BY region ORDER BY region) target', [$activePeriod->id])->fetchObject()?->total_target;
    $profiles = $profiles->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_2" AND profile_stages.result = "Diajukan" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_3")');
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_2" AND profile_stages.result = "Diajukan" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_3")');

    $stageDataLeftJoin = "(
        SELECT *
        FROM (
            SELECT
                h.*,
                ROW_NUMBER() OVER (
                    PARTITION BY h.profile_period_id
                    ORDER BY h.created_at DESC, h.id DESC
                ) AS rn
            FROM profile_stages h WHERE name IN ('stage_2','stage_3') ORDER BY name DESC
        ) x
        WHERE x.rn = 1
    ) stages";
}

else if(auth()->can('asesor'))
{
    $data['totalProfileFinish'] = (clone $query)->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_4" AND profile_period_id = profile_periods.id) )')->first()?->total;
    $data['totalProfileReady'] = (clone $query)->whereRaw('profile_periods.stage = "stage_3"')->first()?->total;
    $data['totalProfileProcess'] = (clone $query)->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_4" AND result = "process" AND profile_period_id = profile_periods.id) )')->first()?->total;
    $profiles = $profiles->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_3" AND profile_stages.result = "Sesuai" AND profile_period_id = profile_periods.id) )');
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_3" AND profile_stages.result = "Sesuai" AND profile_period_id = profile_periods.id) )');

    $stageDataLeftJoin = "(
        SELECT *
        FROM (
            SELECT
                h.*,
                ROW_NUMBER() OVER (
                    PARTITION BY h.profile_period_id
                    ORDER BY h.created_at DESC, h.id DESC
                ) AS rn
            FROM profile_stages h WHERE name IN ('stage_3','stage_4') ORDER BY name DESC
        ) x
        WHERE x.rn = 1
    ) stages";
}

else if(auth()->can('opd'))
{
    $data['totalProfileScheduled'] = (clone $query)->whereRaw('profile_periods.stage = "stage_5" AND profile_periods.status = "Dijadwalkan"')->first()?->total;
    $data['totalProfileNeedCheck'] = (clone $query)->whereRaw('profile_periods.stage = "stage_5" AND profile_periods.status = "Menunggu Verifikasi"')->first()?->total;
    $data['totalProfileDone'] = (clone $query)->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_5" AND profile_period_id = profile_periods.id) )')->first()?->total;

    $profiles = $profiles->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_5" AND profile_period_id = profile_periods.id) ) OR profile_periods.stage = "stage_5"');
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_5" AND profile_period_id = profile_periods.id) ) OR profile_periods.stage = "stage_5"');

    $stageDataLeftJoin = "(
        SELECT *
        FROM (
            SELECT
                h.*,
                ROW_NUMBER() OVER (
                    PARTITION BY h.profile_period_id
                    ORDER BY h.created_at DESC, h.id DESC
                ) AS rn
            FROM profile_stages h WHERE name IN ('stage_5') ORDER BY name DESC
        ) x
        WHERE x.rn = 1
    ) stages";
}

$profiles = $profiles->leftJoin($stageDataLeftJoin, 'stages.profile_period_id','=','profile_periods.id')->get();
$profiles = array_map(function($profile){
    $profile->stage_data = $profile->stage_data ? json_decode($profile->stage_data) : [];
    return $profile;

}, $profiles);

$data['totalProfile'] = $query->first()?->total ?? 0;
$data['profiles'] = $profiles;

return Response::json(__('Data retrieved.'), $data);