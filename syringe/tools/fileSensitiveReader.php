<?php
/**
 * Created by PhpStorm.
 * User: yuanping3
 * Date: 16/6/16
 * Time: 上午11:28
 * Detail: 
 */



function readFilePath($offset)
{
    $path = "/Users/wenqing/Documents/php/ceshi.log";
    $handle = fopen($path, 'r');
    fseek($handle, $offset);
    while(!feof($handle)){
        $line = fgets($handle, 1024);
        if (!empty($line))
        {
            echo $line;
        }
    }
    $offset = ftell($handle);
    fclose($handle);
    return $offset;
}


function run()
{
    $offset = 0;
    while (true)
    {
        sleep(5);
        try
        {
            $offset = readFilePath($offset);
            echo $offset . "\n";
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }
}


run();