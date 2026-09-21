<?php

use Libraries\Database as DB;
use Libraries\Response;

$data = request()->body();
$activePeriod = request()->otherData('activePeriod');
$user = request()->user();
$files = request()->file('documents');

try {
    //code...
    DB::beginTransaction();

    $profileId = DB::table('profiles')->insert($data['profile']);

    $profilePeriodId = DB::table('profile_periods')->insert([
        ...$data['periods'],
        'profile_id' => $profileId,
        'period_id' => $activePeriod->id,
        'stage' => 'stage_1',
        // 'status' => 'Menunggu Verifikasi'
    ]);
    
    DB::table('profile_businesses')->insert([
        ...$data['business'],
        'profile_period_id' => $profilePeriodId,
    ]);

    DB::table('profile_assessments')->insert([
        ...$data['assessments'],
        'profile_period_id' => $profilePeriodId,
        'assessor_id' => $user->id,
        'assessor_name' => $user->name,
    ]);

    DB::table('profile_purposes')->insert([
        ...$data['purposes'],
        'profile_period_id' => $profilePeriodId,
    ]);

    foreach($data['budgets']['description'] as $index => $description)
    {
        $price = parseCurrency($data['budgets']['price'][$index]);
        $budget = [
            'profile_period_id' => $profilePeriodId,
            'description' => $description,
            'price' => $price,
            'unit' => $data['budgets']['unit'][$index],
            'qty' => $data['budgets']['qty'][$index],
            // 'total_price' => $price * $data['budgets']['qty'][$index],
        ];
        DB::table('profile_budgets')->insert($budget);
    }

    foreach($data['income']['description'] as $index => $description)
    {
        DB::table('profile_incomes')->insert([
            'profile_period_id' => $profilePeriodId,
            'description' => $description,
            'amount' => $data['income']['amount'][$index],
        ]);
    }

    foreach ($files['name'] as $key => $name) {

        $file = getUploadedFile($files, $key);

        $doc = uploadFile($file, 'uploads');

        DB::table('profile_documents')->insert([
            'profile_period_id' => $profilePeriodId,
            'name' => $key,
            'file_url' => $doc['path'],
        ]);
    }

    DB::commit();
} catch (\Throwable $th) {
    //throw $th;
    DB::rollBack();
    return Response::json($th->getMessage(), $data, 400);
}

return Response::json(__('Berhasil menyimpan profile'), []);