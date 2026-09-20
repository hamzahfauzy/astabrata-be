<?php

use Libraries\Database as DB;
$isFreshFlag = isset($args[2]) ? $args[2] : '';

try {
    if($isFreshFlag == '--fresh')
    {
        $query = "SELECT CONCAT('DROP TABLE IF EXISTS `', table_name, '`;') as _query
        FROM information_schema.tables
        WHERE table_schema = ?";
        $allDbName = DB::exec($query, [
            env('DB_NAME')
        ])->fetchAll();

        foreach($allDbName as $q)
        {
            $dbQuery = "SET foreign_key_checks = 0;".$q->_query;
            DB::exec($dbQuery);
        }
    }
    
    $query = "CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(100) NOT NULL,
        execute_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )";
    DB::exec($query);

} catch (\Throwable $th) {
    echo "Initiation error\n";
    throw $th;
}

$isRun = false;
$migrationFile = "";
try {
        //code...
    $folder = "databases/migrations";
    if(file_exists($folder))
    {
        $files = preg_grep('/\.sql$/i', scandir($folder));
        
        if(!empty($files))
        {
            $files = array_map(function($file) use ($folder){
                return $folder . '/' . $file;
            }, $files);
    
            $all_migrations = DB::table('migrations')->whereIn('filename', $files)->get();
        
            $all_migrations = array_map(function($migration){
                return $migration->filename;
            }, $all_migrations);
        
            foreach($files as $file)
            {
                if(in_array($file, $all_migrations)) continue;
                $migrationFile = $file;
        
                $myfile = fopen($file, "r") or die("Unable to open file!");
                $query  = fread($myfile,filesize($file));
                fclose($myfile);
                
                DB::exec($query);
                
                DB::table('migrations')->insert([
                    'filename' => $file
                ]);
    
                $isRun = true;
                
                echo "File $file: Migration Success\n";
            }
        
        }
    }
} catch (\Throwable $th) {
    echo "File : ".$migrationFile."\n";
    throw $th;
}

if(!$isRun)
{
    echo "Nothing to migrate\n";
}