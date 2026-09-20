<?php

use Libraries\Database as DB;
use Libraries\Response;

$request = request();
$id = $request->params('id');
$activePeriod = $request->otherData('activePeriod');
$data = DB::table('profiles')
    ->select('
    profiles.*,
    profile_periods.id profile_period_id,

    profile_periods.serial_number period_serial_number,
    profile_periods.stage period_stage,
    profile_periods.status period_status,
    profile_periods.program_type period_program_type,
    profile_periods.phone period_phone,
    profile_periods.address period_address,
    profile_periods.village period_village,
    profile_periods.region period_region,
    profile_periods.regency period_regency,
    profile_periods.province period_province,
    profile_periods.education period_education,
    profile_periods.family_dependent_number period_family_dependent_number,
    profile_periods.social_assistance_type period_social_assistance_type,
    profile_periods.business_assistance_type period_business_assistance_type,
    profile_periods.has_bank_account period_has_bank_account,
    profile_periods.bank_account_name period_bank_account_name,
    profile_periods.bank_account_number period_bank_account_number,
    profile_periods.bank_name period_bank_name,

    profile_businesses.is_active business_is_active,
    profile_businesses.cluster business_cluster,
    profile_businesses.product business_product,
    profile_businesses.manager business_manager,
    profile_businesses.is_location_in_home business_is_location_in_home,
    profile_businesses.address business_address,
    profile_businesses.village business_village,
    profile_businesses.region business_region,
    profile_businesses.regency business_regency,
    profile_businesses.province business_province,
    profile_businesses.start_month business_start_month,
    profile_businesses.surface_area business_surface_area,
    profile_businesses.building_area business_building_area,
    profile_businesses.electricity business_electricity,
    profile_businesses.has_employee business_has_employee,
    profile_businesses.num_of_employee business_num_of_employee,
    profile_businesses.daily_production business_daily_production,
    profile_businesses.legal business_legal,

    profile_assessments.assessor_name assessment_assessor_name,
    profile_assessments.assessment_person assessment_assessment_person,
    profile_assessments.geo_tag assessment_geo_tag,
    profile_assessments.address assessment_address,

    profile_purposes.issue purposes_issue,
    profile_purposes.goals purposes_goals,
    profile_purposes.notes purposes_notes,
    profile_purposes.training_needs purposes_training_needs
    ')
    ->leftJoin('profile_periods', 'profile_periods.profile_id', '=', 'profiles.id')
    ->leftJoin('profile_assessments', 'profile_assessments.profile_period_id', '=', 'profile_periods.id')
    ->leftJoin('profile_businesses', 'profile_businesses.profile_period_id', '=', 'profile_periods.id')
    ->leftJoin('profile_purposes', 'profile_purposes.profile_period_id', '=', 'profile_periods.id')
    // ->leftJoin('profile_documents', 'profile_documents.profile_period_id', '=', 'profile_periods.id')
    // ->leftJoin('profile_budgets', 'profile_budgets.profile_period_id', '=', 'profile_periods.id')
    // ->leftJoin('profile_incomes', 'profile_incomes.profile_period_id', '=', 'profile_periods.id')
    ->where('profile_periods.profile_id', $id)
    ->where('profile_periods.period_id', $activePeriod->id)
    ->first();

$data->documents = DB::table('profile_documents')->select('*')
    ->where('profile_period_id', $data->profile_period_id)
    ->get();

$data->budgets = DB::table('profile_budgets')->select('*')
    ->where('profile_period_id', $data->profile_period_id)
    ->get();

$data->incomes = DB::table('profile_incomes')->select('*')
    ->where('profile_period_id', $data->profile_period_id)
    ->get();

return Response::json(__('data retrieved'), $data);