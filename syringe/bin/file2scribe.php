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

//main();
writeBinary();

function main()
{
    $logFile = './input.log';

    $spanConfigArray = getConfigDataFromLog($logFile);
//    var_dump($spanConfigArray);
//    exit;


//    $spanConfig1 =  array(
//        'trace_id' => 844180087144431,
//        'id' => 844180087144431,
//        'name' => 'apple',
//        'parent_id' => 0,
//        'annotation' => array(
//            array(
//                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
//                'port' => 80,
//                'timestamp' => floatval('1.4653844180087E+15'),
//            ),
//        ),
//    );
//
//    usleep(500);
//
//    $spanConfig2 =  array(
//        'trace_id' => 844180087144431,
//        'id' => 844180087144432,
//        'name' => 'apple_fan',
//        'parent_id' => 844180087144431,
//        'annotation' => array(
//            array(
//                'type' => $GLOBALS['zipkinCore_CONSTANTS']['CLIENT_SEND'],
//                'value' => 'test',
//                'port' => 80,
//                'timestamp' => floatval('1.4653844180169E+15'),
//
//            ),
//        ),
//    );
//
//    $spanConfig3 =  array(
//        'trace_id' => 844180087144431,
//        'id' => 844180087144432,
//        'name' => 'apple_fan',
//        'parent_id' => 844180087144431,
//        'annotation' => array(
//            array(
//                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_RECV'],
//                'value' => 'test',
//                'port' => 80,
//                'timestamp' => floatval('1.4653844180971E+15'),
//
//            ),
//        ),
//    );
//
////    $spanConfigLog =  array(
////        'trace_id' => 844180087144431,
////        'id' => 844180087144432,
////        'name' => 'apple_fan',
////        'parent_id' => 844180087144431,
////        'annotation' => array(
////            array(
////                'type' => 'ft',
////                'value' => 'test',
////                'port' => 80,
////                'timestamp' => floatval('1.4653844181071E+15'),
////
////            ),
////        ),
////    );
//
//    $spanConfig4 =  array(
//        'trace_id' => 844180087144431,
//        'id' => 844180087144432,
//        'name' => 'apple_fan',
//        'parent_id' => 844180087144431,
//        'annotation' => array(
//            array(
//                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
//                'value' => 'test',
//                'port' => 80,
//                'timestamp' => floatval('1.4653844181392E+15'),
//
//            ),
//        ),
//    );
//
//    $spanConfig5 =  array(
//        'trace_id' => 844180087144432,
//        'id' => 844180087144421,
//        'name' => 'apple_fan',
//        'parent_id' => 844180087144431,
//        'annotation' => array(
//            array(
//                'type' => $GLOBALS['zipkinCore_CONSTANTS']['CLIENT_RECV'],
//                'value' => 'test',
//                'port' => 80,
//                'timestamp' => floatval('1.4653844182008E+15'),
//            ),
//        ),
//    );
//
//    $spanConfigTest =  array(
//        'trace_id' => 844180087144431,
//        'id' => 844180087144421,
//        'name' => 'apple_fan',
//        'parent_id' => 844180087144431,
//        'annotation' => array(
//            $spanConfig2['annotation'][0],
//            $spanConfig3['annotation'][0],
//            $spanConfig4['annotation'][0],
//            $spanConfig5['annotation'][0],
////            array(
////                'type' => $GLOBALS['zipkinCore_CONSTANTS']['CLIENT_RECV'],
////                'value' => 'test',
////                'port' => 80,
////                'timestamp' => floatval('1.4653844182008E+15'),
////            ),
//        ),
//    );
//
//    usleep(300);
//
//    $spanConfig6 =  array(
//        'trace_id' => 844180087144431,
//        'id' => 844180087144431,
//        'name' => 'apple',
//        'parent_id' => 0,
//        'annotation' => array(
//            array(
//                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_RECV'],
//                'port' => 80,
//                'timestamp' => floatval('1.4653844182191E+15'),
//            ),
//        ),
//    );
//
//    $spanConfigArray = array(
//        array(
//            'span_name' => 'testxx.mall.auto.sina.com.cn',
//            'config' => $spanConfig1,
//        ),
//        array(
//            'span_name' => 'testxx.car.weibo.com',
//            'config' => $spanConfigTest,
//        ),
////        array(
////            'span_name' => 'testxx.car.weibo.com',
////            'config' => $spanConfigLog,
////        ),
//
////        array(
////            'span_name' => 'testxx.car.weibo.com',
////            'config' => $spanConfig3,
////        ),
////        array(
////            'span_name' => 'testxx.car.weibo.com',
////            'config' => $spanConfig4,
////        ),
////        array(
////            'span_name' => 'testxx.car.weibo.com',
////            'config' => $spanConfig5,
////        ),
//        array(
//            'span_name' => 'testxx.mall.auto.sina.com.cn',
//            'config' => $spanConfig6,
//        ),
//    );

//    var_dump($spanConfigArray);exit;
    //exit;

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
    var_dump($spanConfigArray);

    foreach ($spanConfigArray as $spanData) {
        echo "-----------------------------";
        $transport->open();                                         //开数据结构
        $buf = new TMemoryBuffer();
        $buf->open();
        $transport2 = new TFramedTransport($buf,true,FALSE);
        $protocol2 = new TBinaryProtocol($transport2, true, true);
        $lentry = new LogEntry();
        $lentry->category = "zipkin";


        $span = SpanFormat::getSpan($spanData['span_name'], $spanData['config']);//domain & test
        var_dump($span);
        $span->write($protocol2);
        $message_array = $buf->getBuffer();
        echo "xxxxxxx\n";
//        var_dump($message_array);
        $lentry->message = base64_encode($message_array);
        $lentry->message = 'CgABAAIxWmgdYo4LAAMAAAADR0VUCgAEAAIxWnOc0dgKAAUAAjFaaB1ijg8ABgwAAAACCgABAAU1SM+p91sLAAIAAAACc3IMAAMIAAEK0u61BgACAFALAAMAAAANY2FyLndlaWJvLmNvbQAACgABAAU1SM+qIagLAAIAAAACc3MMAAMIAAEK0u61BgACAFALAAMAAAANY2FyLndlaWJvLmNvbQAADwAIDAAAAAAA';
//        var_dump($lentry->message);exit;
        $lognetry_array = array($lentry);
//        var_dump($lognetry_array);
        $client->Log($lognetry_array);
//      $lentry->message = base64_encode($buf->getBuffer());
//      $lognetry_array = array($lentry);
//
//      $buf->close();


//      $client->Log($lognetry_array);
        $transport->close();
        exit;

        echo "========================";
    }
}

function writeBinary()
{
    $socket = new TSocket('10.227.88.174', 9410);               //连接zipkin collector
    $transport = new TFramedTransport($socket, 6024, 6024);     //按照chunk传输数据的通道
    $protocol = new TBinaryProtocol($transport);                //thrift 二进制协议
    $client = new ZipkinCollectorClient($protocol);


    $transport->open();                                         //开数据结构
    $buf = new TMemoryBuffer();
    $buf->open();
    $transport2 = new TFramedTransport($buf,true,FALSE);
//    $protocol2 = new TBinaryProtocol($transport2, true, true);
    $lentry = new LogEntry();
    $lentry->category = "zipkin";


//    $span = SpanFormat::getSpan($spanData['span_name'], $spanData['config']);//domain & test
//    var_dump($span);
//    $span->write($protocol2);
//    $message_array = $buf->getBuffer();
    echo "xxxxxxx\n";
//        var_dump($message_array);
//    $lentry->message = base64_encode($message_array);
    $lentry->message = 'CgABAAIxWmgdYo4LAAMAAAADR0VUCgAEAAIxWnOc0dgKAAUAAjFaaB1ijg8ABgwAAAACCgABAAU1SM+p91sLAAIAAAACc3IMAAMIAAEK0u61BgACAFALAAMAAAANY2FyLndlaWJvLmNvbQAACgABAAU1SM+qIagLAAIAAAACc3MMAAMIAAEK0u61BgACAFALAAMAAAANY2FyLndlaWJvLmNvbQAADwAIDAAAAAAA';
//        var_dump($lentry->message);exit;
    $lognetry_array = array($lentry);
//        var_dump($lognetry_array);
    $client->Log($lognetry_array);
//      $lentry->message = base64_encode($buf->getBuffer());
//      $lognetry_array = array($lentry);
//
//      $buf->close();


//      $client->Log($lognetry_array);
    $transport->close();
    exit;


}


/**
 * 从本地文件获取trace+span的config数据
 * @param $file
 */
function getConfigDataFromLog($file = null)
{
    $configData = array();

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

        $logStr =  trim(fgets($fd));
        if (empty($logStr))
            continue;

        $tmp = explode('{', $logStr);
        $tmp = $tmp[1];
        $tmp = explode('}', $tmp);
        $tmp = $tmp[0];
//    $spanConfig1 =  array(
//        'trace_id' => 11114,
//        'id' => 11114,
//        'name' => 'apple',
//        'parent_id' => 0,
//        'annotation' => array(
//            array(
//                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
//                'port' => 80,
//                'timestamp' => (microtime(true) * 1000000),
//            ),
//        ),
//    );
        $oriData = explode('|', $tmp);
        $unitData['span_name'] = $oriData[3];//domain
        $unitData['config'] = array();
        $singleConfig['trace_id'] = intval($oriData[0]);
        $singleConfig['id'] = intval($oriData[1]);
        $singleConfig['name'] = trim($oriData[6]);//test
        $singleConfig['parent_id'] = intval($oriData[2]);
        $singleConfig['annotation'] = array(
            array(
                'type' => $oriData[5],
                'port' => intval($oriData[7]),
                'timestamp' => floatval($oriData[4]),
            )
        );
        $unitData['config'] = $singleConfig;

//        foreach ($configData as $key => $compareData)
//        {
//            if (($compareData['span_name'] == $unitData['span_name'])
//                    && ($compareData['config']['trace_id'] == $unitData['config']['trace_id'])
//                    && ($compareData['config']['id'] == $unitData['config']['id'])
//                    && ($compareData['config']['parent_id'] == $unitData['config']['parent_id'])
//                    && ($compareData['config']['parent_id'] != 0)
//            )
//            {
//                $configData[$key]['config']['annotation'][] = $unitData['config']['annotation'][0];
//                $handled = 1;
//                break;
//            }
//        }
//
//        if(!$handled)
//            $configData[] = $unitData;

        $configData[] = $unitData;

//array(8) {
//            [0]=>
//    string(15) "844180087144417"
//            [1]=>
//    string(15) "844180087144417"
//            [2]=>
//    string(1) "0"
//            [3]=>
//    string(21) "mall.auto.sina.com.cn"
//            [4]=>
//    string(19) "1.4653844182191E+15"
//            [5]=>
//    string(2) "ss"
//            [6]=>
//    string(5) "apple"
//            [7]=>
//    string(2) "80"
//  }

    }

    $result[0] = $configData[0];
    $result[0]['config']['annotation'][] = $configData[3]['config']['annotation'][0];
//    $result[0]['config']['annotation'][] = $configData[4]['config']['annotation'][0];
    $result[1] = $configData[1];
    $result[1]['config']['annotation'][] = $configData[2]['config']['annotation'][0];
//    $result[1]['config']['annotation'][] = $configData[3]['config']['annotation'][0];
//    $result[2] = $configData[0];
//    $result[2]['config']['annotation'][] = $configData[6]['config']['annotation'][0];

//    $result[3] = $configData[6];

    fclose($fd);
    return $result;
}

///**
// * 从本地文件获取trace+span的config数据
// * @param $file
// */
//function getConfigDataFromLog($file = null)
//{
//    $configData = array();
//
//    $file = trim($file);
//    if (empty($file) || !is_readable($file))
//    {
//        echo "log file can't be null or unreadable!\n";
//        exit;
//    }
//
//    $fd = fopen($file, 'r');
//    while(!feof($fd)){
//        $unitData = array();
//        $logStr = '';
//        $handled = 0;
//
//        $logStr =  trim(fgets($fd));
//        if (empty($logStr))
//            continue;
//
//        $tmp = explode('{', $logStr);
//        $tmp = $tmp[1];
//        $tmp = explode('}', $tmp);
//        $tmp = $tmp[0];
////    $spanConfig1 =  array(
////        'trace_id' => 11114,
////        'id' => 11114,
////        'name' => 'apple',
////        'parent_id' => 0,
////        'annotation' => array(
////            array(
////                'type' => $GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'],
////                'port' => 80,
////                'timestamp' => (microtime(true) * 1000000),
////            ),
////        ),
////    );
//        $oriData = explode('|', $tmp);
//        $unitData['span_name'] = $oriData[3];
//        $unitData['config'] = array();
//        $singleConfig['trace_id'] = intval($oriData[0]);
//        $singleConfig['id'] = intval($oriData[1]);
//        $singleConfig['name'] = trim($oriData[6]);
//        $singleConfig['parent_id'] = intval($oriData[2]);
//        $singleConfig['annotation'] = array(
//            array(
//                'type' => $oriData[5],
//                'port' => intval($oriData[7]),
//                'timestamp' => floatval($oriData[4]),
//            )
//        );
//        $unitData['config'] = $singleConfig;
//
//        foreach ($configData as $key => $compareData)
//        {
//            if (($compareData['span_name'] == $unitData['span_name'])
//                && ($compareData['config']['trace_id'] == $unitData['config']['trace_id'])
//                && ($compareData['config']['id'] == $unitData['config']['id'])
//                && ($compareData['config']['parent_id'] == $unitData['config']['parent_id'])
//                && ($compareData['config']['parent_id'] != 0)
//            )
//            {
//                $configData[$key]['config']['annotation'][] = $unitData['config']['annotation'][0];
//                $handled = 1;
//                break;
//            }
//
//        }
//
//        if(!$handled)
//            $configData[] = $unitData;
//
//
//
//
////array(8) {
////            [0]=>
////    string(15) "844180087144417"
////            [1]=>
////    string(15) "844180087144417"
////            [2]=>
////    string(1) "0"
////            [3]=>
////    string(21) "mall.auto.sina.com.cn"
////            [4]=>
////    string(19) "1.4653844182191E+15"
////            [5]=>
////    string(2) "ss"
////            [6]=>
////    string(5) "apple"
////            [7]=>
////    string(2) "80"
////  }
//
//    }
//
//    fclose($fd);
//    return $configData;
//}







