<?php

use Libraries\Database as DB;
use Libraries\Services\DatabaseService;

$user = auth()->user();
$activePeriod = request()->otherData('activePeriod');
$query = DB::table('profiles')->select('profiles.*')
        ->leftJoin('profile_periods','profile_periods.profile_id','=','profiles.id')
        ->where('profile_periods.period_id','=',$activePeriod->id);
        // ->leftJoin('profile_stages','profile_stages.profile_period_id','=','profile_periods.id')

if(auth()->can('desa'))
{
    $assigment = DB::table('user_assignment')
        ->select('user_assignment.*, villages.name village_name, regions.name region_name')
        ->leftJoin('villages','villages.id','=','user_assignment.model_id')
        ->leftJoin('regions','regions.id','=','villages.region_id')
        ->where('user_id','=',$user->id)->first();

    $query = $query->where('profile_periods.region','=',$assigment->region_name)
        ->where('profile_periods.village','=',$assigment->village_name);
}

if(auth()->can('pendamping'))
{
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_1" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_1")');
}

else if(auth()->can('kecamatan'))
{
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_2" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_2")');
}

$lists = (new DatabaseService)->listing($query, ['profiles.name','profiles.personal_number','profiles.family_number']);

return [
    'messages' => __('Data retrieved.'),
    ...$lists

];