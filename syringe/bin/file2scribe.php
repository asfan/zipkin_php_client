<?php
/**
 * Created by PhpStorm.
 * User: yuanping3
 * Date: 16/6/7
 * Time: 下午2:38
 * Detail: 读取文件写入scribe
 */

$ROOT_PATH = dirname(__FILE__) . '/../';
include_once $ROOT_PATH . '/tools/traceFormat.php';

$GLOBALS['THRIFT_ROOT_'] = '/Users/fanyuanping/Workbench/git_repository/zipkin_php_client/include/zipkin'; //$ini_array["includepath"].'/zipkin';
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

main();

function main()
{
    $spanConfig1 =  array(
        'trace_id' => 11114,
        'id' => 11114,
        'name' => 'apple',
        'parent_id' => 0,
        'annotation' => array(
            array(
                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
                'port' => 80,
                'timestamp' => (microtime(true) * 1000000),
            ),
        ),
    );

    usleep(500);

    $spanConfig2 =  array(
        'trace_id' => 11114,
        'id' => 21114,
        'name' => 'apple_fan',
        'parent_id' => 11114,
        'annotation' => array(
            array(
                'type' => $GLOBALS['zipkinCore_CONSTANTS']['CLIENT_SEND'],
                'value' => 'test',
                'port' => 80,
                'timestamp' => (microtime(true) * 1000000),

            ),
            array(
                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_RECV'],
                'value' => 'test',
                'port' => 80,
                'timestamp' => (microtime(true) * 1000000),

            ),
            array(
                'type' => 'test',
                'value' => 'test',
                'port' => 80,
                'timestamp' => (microtime(true) * 1000000),

            ),
            array(
                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
                'value' => 'test',
                'port' => 80,
                'timestamp' => (microtime(true) * 1000000),

            ),
            array(
                'type' => $GLOBALS['zipkinCore_CONSTANTS']['CLIENT_RECV'],
                'value' => 'test',
                'port' => 80,
                'timestamp' => (microtime(true) * 1000000),
            ),
        ),
    );

    usleep(300);

    $spanConfig3 =  array(
        'trace_id' => 11114,
        'id' => 11114,
        'name' => 'apple',
        'parent_id' => 0,
        'annotation' => array(
            array(
                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_RECV'],
                'port' => 80,
                'timestamp' => (microtime(true) * 1000000),
            ),
        ),
    );

    $spanConfigArray = array(
        array(
            'span_name' => 'yuanping1',
            'config' => $spanConfig1,
        ),
        array(
            'span_name' => 'yuanping2',
            'config' => $spanConfig2,
        ),
        array(
            'span_name' => 'yuanping1',
            'config' => $spanConfig3,
        ),
    );

    //构造span1
//    $span1 = SpanFormat::getSpan('yuanping1', $spanConfig1);
    //构造span2
//    $span2 = SpanFormat::getSpan('yuanping2', $spanConfig2);
//    var_dump($span1);
//    $span1->annotations = array_merge($span1->annotations,
//        array(
//            'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVICE_SEND'],
//            'port' => 80,
//        )
//    );

        //echo "pop:".$span->name."<br/>";
    $socket = new TSocket('10.227.88.174', 9410);               //连接zipkin collector
    $transport = new TFramedTransport($socket, 6024, 6024);     //按照chunk传输数据的通道
    $protocol = new TBinaryProtocol($transport);                //thrift 二进制协议
    $client = new ZipkinCollectorClient($protocol);             //创建客户端

    foreach ($spanConfigArray as $spanData) {
        echo "-----------------------------";
        $transport->open();                                         //开数据结构
        $buf = new TMemoryBuffer();
        $buf->open();
        $transport2 = new TFramedTransport($buf,true,FALSE);
        $protocol2 = new TBinaryProtocol($transport2, true, true);
        $lentry = new LogEntry();
        $lentry->category = "zipkin";


        $span = SpanFormat::getSpan($spanData['span_name'], $spanData['config']);
        var_dump($span);
        $span->write($protocol2);
        $lentry->message = base64_encode($buf->getBuffer());
        $lognetry_array = array($lentry);
        var_dump($lognetry_array);
        $client->Log($lognetry_array);
//      $lentry->message = base64_encode($buf->getBuffer());
//      $lognetry_array = array($lentry);
//
//      $buf->close();


//      $client->Log($lognetry_array);
        $transport->close();

        echo "========================";
    }


}