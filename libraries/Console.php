<?php

namespace Libraries;

class Console
{

    public function __construct()
    {
        
    }

    public function run($args)
    {
        require 'libraries/Functions.php';

        $commandFile = null;

        if(isset($args[1]))
        {
            $commandFile = $args[1] .'.php';
            if(file_exists($commandFile))
            {
                require $commandFile;
            }
            else
            {
                echo "File $commandFile.php not found\n";
            }
        }

        die;
    }

}