<?php

namespace Libraries\Middlewares;

use Libraries\Request;

class CrudConfig
{
    public static function handle(Request $request, $config): void
    {
        $request->setOtherData('crudConfig', $config);
    }
}