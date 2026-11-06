<?php

namespace App\Master;

class PeriodController {

    public static function config(){
        return [
            'data' => [
                'table' => 'periods'
            ],
            
            'actions' => [
                // index, store, update, destroy, show
            ]
        ];
    }

}