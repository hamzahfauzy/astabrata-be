<?php

use Libraries\Database as DB;
use Libraries\Services\DatabaseService;

$activePeriod = request()->otherData('activePeriod');
$query = DB::table('profiles')->select('profiles.*')
        ->leftJoin('profile_periods','profile_periods.profile_id','=','profiles.id')
        ->where('profile_periods.period_id','=',$activePeriod->id);
        // ->leftJoin('profile_stages','profile_stages.profile_period_id','=','profile_periods.id')

if(auth()->can('pendamping'))
{
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_1" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_1")');
}

else if(auth()->can('kecamatan'))
{
    $query = $query->whereRaw('(EXISTS (SELECT 1 FROM profile_stages WHERE name = "stage_2" AND profile_period_id = profile_periods.id) OR profile_periods.stage = "stage_2")');
}

$lists = (new DatabaseService)->listing($query);

return [
    'messages' => __('Data retrieved.'),
    ...$lists

];