<?php

use Dotenv\Dotenv;
use Libraries\Auth;
use Libraries\Exceptions\UnauthorizedException;
use Libraries\Request;
use Libraries\Database as DB;
use Libraries\Exceptions\ValidationException;

if(file_exists('vendor/autoload.php'))
{
    require 'vendor/autoload.php';
}

$dotenv = Dotenv::createImmutable(__DIR__ . '//../');
$dotenv->safeLoad();

function app(string $key, $default = null)
{
    $config = config('app');
    return isset($config[$key]) ? $config[$key] : $default;
}

function env(string $key, $default = null)
{
    return isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : $default);
}

function config(string $key)
{
    
    $file = 'config/' . $key .'.php';

    if(file_exists($file))
    {
        return require $file;
    }

    return [];
}

function request(): Request
{
    static $request = null;

    if ($request === null) {
        $request = new Request();
    }

    return $request;
}

function auth()
{
    static $auth;

    if (!$auth) {
        $auth = new Auth();
    }

    return $auth;
}

function isAuthenticated()
{
    return function(Request $request){
        $user = auth()->user();
        if(!$user)
        {
            throw new UnauthorizedException([
                'message' => 'Unauthorized'
            ]);
        }

        $request->setUser($user);
    };
}

function periodActive()
{
    return function(Request $request){
        $period = DB::table('periods')->select('*')->where('status', '=', 'ACTIVE')->first();
        if(!$period)
        {
            throw new ValidationException([
                'active_period' => 'No Active Period Found.'
            ]);
        }

        $request->setOtherData('activePeriod', $period);
    };
}

function can($permission)
{
    return function (Request $request) use ($permission) {
        return auth()->can($permission);
    };
}

function permissionMiddleware($permission)
{
    return function(Request $request) use ($permission){
        if(!auth()->can($permission))
        {
            throw new UnauthorizedException([
                'message' => ' Unauthorized'
            ]);
        }

        return true;
    };
}

function validate($file){
    return function (Request $request) use ($file) {
        if(file_exists($file .'.php'))
        {
            $rules = require $file .'.php';
            $request->validate($rules);
        }
    };
}

function crudPermission($prefix)
{
    return function (Request $request) use ($prefix) {
        $request->setOtherData('crud_permission_'.$prefix, $prefix);
    };
}

function crudConfig(array $config){
    return function (Request $request) use ($config) {
        $request->setOtherData('crudConfig', $config);
    };
}


/** 
 * Get header Authorization
 * */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/**
 * get access token from header
 * */
function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function __($string)
{
    return $string;
}

function parseCurrency(string $value)
{
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);

    return (float) $value;
}

function uploadFile(array $file, string $uploadDir): array
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload file gagal.');
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $originalName = $file['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Nama file baru
    $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;

    $destination = rtrim($uploadDir, DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR
        . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Gagal memindahkan file upload.');
    }

    return [
        'original_name' => $originalName,
        'filename'      => $newFilename,
        'path'          => $destination,
        'mime_type'     => $file['type'],
        'size'          => $file['size'],
    ];
}

function getUploadedFile(array $files, string $key): array
{
    return [
        'name' => $files['name'][$key],
        'type' => $files['type'][$key],
        'tmp_name' => $files['tmp_name'][$key],
        'error' => $files['error'][$key],
        'size' => $files['size'][$key],
    ];
}