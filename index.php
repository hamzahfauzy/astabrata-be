<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
{
    http_response_code(204);
    die;
}

function my_autoloader(string $class) {

    // explode namespace
    $classes = explode('\\', $class);
    $importClass = null;
    if(in_array($classes[0], ['Libraries','Modules','App']))
    {
        $classType = $classes[0];
        unset($classes[0]);
        if($classType == 'Libraries')
        {
            $importClass = 'libraries/' . implode('/',$classes);
        }

        else if($classType == 'App')
        {
            $importClass = 'app/' . implode('/',$classes);
        }

        else if($classType == 'Modules')
        {
            $classes[1] = strtolower($classes[1]);
            $classes[2] = strtolower($classes[2]);
            
            $importClass = 'modules/' . implode('/',$classes);
        }

        if(file_exists($importClass.'.php'))
        {
            require $importClass.'.php';
        }
        else
        {
            die($class . ' is not valid');
        }
    }
}

spl_autoload_register('my_autoloader');

$app = new \Libraries\Application();

$app->run();