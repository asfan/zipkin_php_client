<?php
/**
 * Created by PhpStorm.
 * User: yuanping3
 * Date: 16/6/10
 * Time: 下午9:21
 * Detail: 日志监控进程
 */

$ROOT_PATH = dirname(__FILE__) . '/../';
include_once $ROOT_PATH . '/tools/traceFormat.php';

//$GLOBALS['THRIFT_ROOT_'] = '/Users/fanyuanping/Workbench/git_repository/zipkin_php_client/include/zipkin'; //$ini_array["includepath"].'/zipkin';
$GLOBALS['THRIFT_ROOT_'] = dirname(dirname(dirname(__FILE__))) . '/include/zipkin/';
require_once $GLOBALS['THRIFT_ROOT_'].'/Thrift.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TPhpStream.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/THttpClient.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TFramedTransport.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TMemoryBuffer.php';
//error_reporting(E_NONE);

//包含helloworld接口文件
require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCollector/ZipkinCollector.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCore/zipkinCore_types.php';
require_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/testLogEntry.php';
include_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/mq.php';


$file = $argv[1];
if (empty($file)) {
    echo "please set source file whole path!\n";
    exit;
}

main($file);



function main($file)
{
    //获得pid
    $pid = getmypid();
    $offsetLog = "./zipkin-parse-daemon-{$pid}.pid";
    $offsetFd = fopen($offsetLog, "w+");
    if (empty($offsetFd))
    {
        echo "can't set offset into {$offsetLog}!\n";
        exit;
    }

    $logFile = $file;

    $socket = new TSocket('10.227.88.174', 9410);               //连接zipkin collector
    $transport = new TFramedTransport($socket, 6024, 6024);     //按照chunk传输数据的通道
    $protocol = new TBinaryProtocol($transport);                //thrift 二进制协议
    $client = new ZipkinCollectorClient($protocol);
    $transport->open();                                         //开数据结构

    $offset = 0;

    while (true)
    {
        sleep(3);
        try
        {
            $handle = fopen($logFile, 'r');
            if (empty($handle))
            {
                echo "can't open source file {$handle}!\n";
                exit;
            }

            fseek($handle, $offset);
            while(!feof($handle)){
                $line = fgets($handle, 2048);
                if (!preg_match('/^WTRACE_BINARY::::/', $line, $matches))
                    continue;
                if (!empty($line))
                {
                    $line = str_replace('WTRACE_BINARY::::', '', $line);
                    echo "-----------------------------\n";


                    $lentry = new LogEntry();
                    $lentry->category = "zipkin";
                    var_dump($line);

                    $lentry->message = $line;
                    $lognetry_array = array($lentry);
                    $client->Log($lognetry_array);

                    $transport->close();
                }
            }
            $offset = ftell($handle);
            fclose($handle);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            fclose($handle);
        }
    }

//    foreach ($binaryData as $spanData) {
//        echo "-----------------------------";
//
//        $transport->open();                                         //开数据结构
//        $lentry = new LogEntry();
//        $lentry->category = "zipkin";
//        var_dump($spanData);
//
//        $lentry->message = $spanData;
//        $lognetry_array = array($lentry);
//        $client->Log($lognetry_array);
//        $transport->close();
//    }
}


/**
 * 从本地文件获取trace+span的config数据
 * @param $file
 */
function getConfigDataFromLog($file = null)
{
    $result = array();

    $file = trim($file);
    if (empty($file) || !is_readable($file))
    {
        echo "log file can't be null or unreadable!\n";
        exit;
    }

    $fd = fopen($file, 'r');
    while(!feof($fd)){
        $unitData = array();
        $logStr = '';
        $handled = 0;

        $logStr = trim(fgets($fd));
        if (empty($logStr))
            continue;

        $result[] = $logStr;
    }

    fclose($fd);
    return $result;
}