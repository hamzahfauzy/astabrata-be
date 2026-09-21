<?php

use Libraries\Database as DB;
use Libraries\Services\DatabaseService;

$user = auth()->user();
$activePeriod = request()->otherData('activePeriod');

$assigment = DB::table('user_assignment')
    ->select('user_assignment.*, villages.name village_name, regions.name region_name')
    ->leftJoin('villages','villages.id','=','user_assignment.model_id')
    ->leftJoin('regions','regions.id','=','villages.region_id')
    ->where('user_id','=',$user->id)->first();

$query = DB::table('profiles')->select('
profiles.*,
CASE 
    WHEN profile_documents.name = "identity_card" THEN "KTP"
    WHEN profile_documents.name = "family_card" THEN "KK"
    WHEN profile_documents.name = "home_image" THEN "Rumah"
END document_name,
profile_documents.file_url
')
        ->leftJoin('profile_periods','profile_periods.profile_id','=','profiles.id')
        ->leftJoin('profile_documents','profile_documents.profile_period_id','=','profile_periods.id')
        ->where('profile_periods.period_id','=',$activePeriod->id)
        ->where('profile_periods.region','=',$assigment->region_name)
        ->where('profile_periods.village','=',$assigment->village_name);

$lists = (new DatabaseService)->listing($query, ['profiles.name','profiles.personal_number','profiles.family_number','profile_documents.name']);

return [
    'messages' => __('Data retrieved.'),
    ...$lists

];