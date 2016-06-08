<?php
/**
 * Created by PhpStorm.
 * User: yuanping3
 * Date: 16/6/7
 * Time: 下午5:30
 * Detail: 
 */

//$ini_array = parse_ini_file("config.ini");
//$ini_array["zookeeperip"];
//$ini_array["zookeeperport"];

//包含thrift客户端库文件
$ROOT_PATH = dirname(__FILE__) . '/../';

//$GLOBALS['THRIFT_ROOT_'] = '/Users/fanyuanping/Workbench/git_repository/zipkin_php_client/include/zipkin'; //$ini_array["includepath"].'/zipkin';
$GLOBALS['THRIFT_ROOT_'] = $ROOT_PATH;
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
//error_reporting(E_ALL);

$span = MessageQueue::getInstance()->pop();
var_dump($span);

//echo "-->".gettype($span);
if(!is_null($span) && gettype($span)!= 'boolean')
{
    //echo "pop:".$span->name."<br/>";
    echo gettype($span);

    $socket = new TSocket('10.227.88.174', 9410);
    $transport = new TFramedTransport($socket, 6024, 6024);
    $protocol = new TBinaryProtocol($transport);
    $client = new ZipkinCollectorClient($protocol);

//    $my_log = new LogDemo();

    echo "-----------------------------";
    $transport->open();
    $buf = new TMemoryBuffer();
    $buf->open();
    $transport2 = new TFramedTransport($buf,true,FALSE);
    $protocol2 = new TBinaryProtocol($transport2, true, true);

    $span->write($protocol2);

    $lentry = new LogEntry();
    $lentry->category = "zipkin";
    $lentry->message  = base64_encode($buf->getBuffer());

    $lognetry_array = array($lentry);
    $buf->close();


    $client->Log($lognetry_array);
    $transport->close();

    echo "========================";

}

class collectorScribeClient {

    function __construct() {
        $socket = new TSocket('10.227.88.174', 9410);
        $transport = new TFramedTransport($socket, 6024, 6024);
        $protocol = new TBinaryProtocol($transport);
        $client = new ZipkinCollectorClient($protocol);
        $transport->open();
        $buf = new TMemoryBuffer();
        $buf->open();
    }

}

