<?php

use Libraries\Database as DB;
use Libraries\Response;

$data = request()->body();
$activePeriod = request()->otherData('activePeriod');
$user = request()->user();
$files = request()->file('documents');
$profileId = request()->params('id');
$profilePeriod = DB::table('profile_periods')->where('profile_id', '=', $profileId)
->where('period_id', '=', $activePeriod->id)->first();

try {
    //code...
    DB::beginTransaction();

    DB::table('profiles')->where('id', '=', $profileId)->update($data['profile']);

    DB::table('profile_periods')
    ->where('id', '=', $profilePeriod->id)
    ->update([
        ...$data['periods'],
        'stage' => 'stage_1',
    ]);
    
    DB::table('profile_businesses')
    ->where('profile_period_id', '=', $profilePeriod->id)
    ->update([
        ...$data['business'],
    ]);

    DB::table('profile_assessments')
    ->where('profile_period_id', '=', $profilePeriod->id)
    ->update([
        ...$data['assessments'],
    ]);

    DB::table('profile_purposes')
    ->where('profile_period_id', '=', $profilePeriod->id)
    ->update([
        ...$data['purposes'],
    ]);

    DB::table('profile_budgets')->where('profile_period_id', '=', $profilePeriod->id)->delete();
    foreach($data['budgets']['description'] as $index => $description)
    {
        $price = parseCurrency($data['budgets']['price'][$index]);
        $budget = [
            'profile_period_id' => $profilePeriod->id,
            'description' => $description,
            'price' => $price,
            'unit' => $data['budgets']['unit'][$index],
            'qty' => $data['budgets']['qty'][$index],
            // 'total_price' => $price * $data['budgets']['qty'][$index],
        ];
        DB::table('profile_budgets')->insert($budget);
    }

    DB::table('profile_incomes')->where('profile_period_id', '=', $profilePeriod->id)->delete();
    foreach($data['income']['description'] as $index => $description)
    {
        DB::table('profile_incomes')->insert([
            'profile_period_id' => $profilePeriod->id,
            'description' => $description,
            'amount' => $data['income']['amount'][$index],
        ]);
    }

    foreach ($files['name'] as $key => $name) {
        if(!empty($name))
        {
            $file = getUploadedFile($files, $key);
    
            $doc = uploadFile($file, 'uploads');
    
            DB::table('profile_documents')
                ->where('profile_period_id', '=', $profilePeriod->id)
                ->where('name', '=', $key)
                ->update([
                    'file_url' => $doc['path'],
                ]);
        }


    }

    DB::commit();
} catch (\Throwable $th) {
    //throw $th;
    DB::rollBack();
    return Response::json($th->getMessage(), $data, 400);
}

return Response::json(__('Berhasil menyimpan profile'), []);