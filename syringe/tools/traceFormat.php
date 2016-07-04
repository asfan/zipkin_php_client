<?php
/**
 * Created by PhpStorm.
 * User: yuanping3
 * Date: 16/6/7
 * Time: 下午2:30
 * Detail: zipkin系统trace生成工具
 */

$ROOT_PATH = dirname(__FILE__) . '/../';


//$GLOBALS['THRIFT_ROOT_'] = $ini_array["includepath"];
$GLOBALS['SYRINGE_ROOT_PATH'] = $ROOT_PATH;
$GLOBALS['SYRINGE_THRIFT_ROOT_PATH'] =  $ROOT_PATH . '../include/';
$GLOBALS['SYRINGE_CONFIG_PATH'] = $ROOT_PATH;

//数据buffer
$GLOBALS['queue'] = null;

include_once $GLOBALS['SYRINGE_THRIFT_ROOT_PATH'].'/zipkin/Thrift.php';
require_once $GLOBALS['SYRINGE_THRIFT_ROOT_PATH'].'/zipkin/packages/zipkinCore/zipkinCore_constants.php';
require_once $GLOBALS['SYRINGE_THRIFT_ROOT_PATH'].'/zipkin/transport/TMemoryBuffer.php';

//输入config文件
$ini_array = parse_ini_file($GLOBALS['SYRINGE_CONFIG_PATH'] . "config.ini");
//var_dump($ini_array);
//var_dump($GLOBALS);

class SyringeTrace
{
    static private  $SPAN_BUILDER = NULL;

    static private  $instance = NULL;

//    private static  $shmopobj = NULL;
    private static $_queue = null;


    private function  __construct()
    {
        //用个内存队列
        self::$_queue = &$GLOBALS['queue'];
    }

    public static function getInstance ()
    {
        if (is_null(self::$instance)) {
            self::$instance = new SyringeTrace();
        }
        return self::$instance;
    }

    //根据span构造trace,写入到queue里面
    public function setTrace($spanName, $spanConfig = null)
    {
        //max 16k
        if (strlen(self::$_queue) > 1024 * 16) {
            throw new Exception('queue can not be more than 16k');
        }

        SpanFormat::getSpan($spanName, $spanConfig);
    }

    //从queue里面写入到scribe
    public function syringe2Scribe()
    {

    }

    public static function cleanQueue()
    {
        self::$_queue = null;
    }


    //构建span,可能是topspan,也可能不是
//    public  function clientSend($span_name, $span = null)
//    {
//        //判断共享内存中是否 “on”;
//        if (isset(self::$shmopobj->database) && self::$shmopobj->database != null &&  self::$shmopobj->database == "on" )
//        {
//            SpanBuilder::clientSend($span_name, $span);
//        }
//        else
//        {
//            //如果禁止收集的话，那么原先收集的也要清空。
//            SpanBuilder::init();
//        }
//    }
//
//    public  function clientReceive()
//    {
//        //判断共享内存中是否 “on”;
//        if (isset(self::$shmopobj->database) && self::$shmopobj->database != null &&  self::$shmopobj->database=="on")
//        {
//            SpanBuilder::clientReceive();
//        }
//    }
}

class SpanFormat {
    private static $STACK  = null;
    private $_span = null;

    public static function init()
    {
        //建立一个栈
        self::$STACK = new SplStack();
    }

    /**
     * 如果span为空，生成随机span
     * @param $spanName
     * @param null $spanConfig
     * @throws Exception
     */
    public static function getSpan($spanName, $spanConfig = null)
    {
        //单例维护span堆栈
        if (is_null(self::$STACK)) {
            self::init();
        }

        if (empty($spanConfig)) {
            $span = new Span();
            $span->name      = $spanName;
            $span->trace_id  = SpanContext::getCurrentTraceId();
            $span->id        = SpanContext::getCurrentSpanId();
            $span->parent_id = SpanContext::getParentSpanId();

            $span->annotations = array(AnnotationBuilder::clientSendAnnotation($spanName),AnnotationBuilder::serverReceAnnotation($spanName));
            //        MessageQueue::getInstance()->push($span);
        }
        else {

//            if (!isset($spanConfig['annotation_type'])) {
//                throw new Exception('There must be annotation_type in span configure!');
//            }
//
//            if (!isset($spanConfig['endpoint_port'])) {
//                throw new Exception('There must be endpoint_port in span configure!');
//            }
//
//            $type = $spanConfig['annotation_type'];
//            $port = $spanConfig['endpoint_port'];

            $span = new Span($spanConfig);//span->name test
            if (isset($spanConfig['annotation']) && is_array($spanConfig['annotation'])) {
                foreach ($spanConfig['annotation'] as $value) {
                    $span->annotations[] = AnnotationFormat::makeAnnotation($value['type'], $spanName, $value['port'], $value['timestamp']);
                }
            }
        }

//        self::$STACK->push($span);
        return $span;
    }

}

//class SpanBuilder{
//
//    private static $STACK  = NULL ;
//
//    private $_span;
//
//    //$GLOBALS['STACK']
//    public static function init()
//    {
//        //echo "--init--";
//        self::$STACK = new SplStack();
//        //self::$STACK->
//    }
//
//    /**
//     * 如果span为空，生成随机span
//     * @param $span_name
//     * @param null $span
//     */
//    public static function clientSend($span_name, $span = null)
//    {
//        if (is_null(self::$STACK)) {
//            self::init();
//        }
//
//        if (empty($span))
//        {
//            $span = new Span();
//            $span->name     = $span_name;
//            $span->trace_id = SpanContext::getCurrentTraceId();
//            $span->id       = SpanContext::getCurrentSpanId();
//            $span->parent_id= SpanContext::getParentSpanId();
//
//            $span->annotations = array(AnnotationBuilder::clientSendAnnotation($span_name),AnnotationBuilder::serverReceAnnotation($span_name));
//            //        MessageQueue::getInstance()->push($span);
//
//        self::$STACK->push($span);
//
//    }
//
//    public static function clientReceive()
//    {
//        if (is_null(self::$STACK)) {
//            self::init();
//            throw new Exception("STACK  is null ,when call clientReceive function before  The clientSend has  be called");
//        }
//        if(self::$STACK->count() == 0)
//        {
//            throw new Exception("STACK  is empty ,when call clientReceive function before  The clientSend has  be called");
//        }
//
//        $span =  self::$STACK->pop();
//        SpanContext::clearSpanID();
//        //echo gettype($span->annotations);
//        $span->annotations =  array_merge($span->annotations, array(AnnotationBuilder::serverSendAnnotation($span->name),AnnotationBuilder::clientReceAnnotation($span->name)));
//        //$span->annotations->push(AnnotationBuilder::serverSendAnnotation($span->name));
//        //$span->annotations->push(AnnotationBuilder::clientReceAnnotation($span->name));
//
//        MessageQueue::getInstance()->push($span);
////        echo "over-";
//
//
//    }
//
//}

class SpanContext
{
    private static $ID_STACK      = NULL;
    //private static $IS_TOP_SPAN   = TRUE;

    public static function getCurrentTraceId()
    {
        if(is_null($GLOBALS['TRACEID']))
        {
            $GLOBALS['TRACEID'] = Util::getRandInt();
        }
        return  $GLOBALS['TRACEID'];
    }

    public static function getCurrentSpanId()
    {
        if(is_null(self::$ID_STACK))
        {
            self::$ID_STACK = new SplStack();
            self::$ID_STACK->push($GLOBALS['TRACEID']);
            // echo "<br/>getCurrentSpanId1 - >".count($ID_STACK);
            return $GLOBALS['TRACEID'];
        }
        else
        {
            $id = Util::getRandInt();
            self::$ID_STACK->push($id);
            // echo "<br/>getCurrentSpanId2 - >".count($ID_STACK);
            return $id;
        }
    }

    public static function getParentSpanId()
    {
        if(self::isTopSpan())
        {
            return NULL;
        }
        else
        {
            $cid = self::$ID_STACK->pop();
            $pid = self::$ID_STACK->pop();
            self::$ID_STACK->push($pid);
            self::$ID_STACK->push($cid);
            return $pid;
        }

    }

    public static function isTopSpan()
    {
        if(is_null($GLOBALS['TRACEID']) || is_null(self::$ID_STACK))
        {
            //echo "<br/>orrrr";
            return TRUE;
        }
        else
        {
            if(count(self::$ID_STACK)>1)
            {
                // echo "<br/>stack >1";
                return FALSE;
            }
            else
            {
                // echo "<br/> !stack >1".count($ID_STACK);
                return TRUE;
            }
        }
    }

    public static function clearSpanID()
    {
        self::$ID_STACK->pop();
    }

}


//小工厂专门创建annotation
class AnnotationFormat {

    //创建测试span（4个一组 cs&sr一对 cr&ss一对)
    public static function clientSendAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['CLIENT_SEND'], $spanName);
    }

    public static function  serverReceAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['SERVER_RECV'], $spanName);
    }

    public static function  serverSendAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'], $spanName);
    }

    public static function  clientReceAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['CLIENT_RECV'], $spanName);
    }

    public static function makeAnnotation($type, $spanName, $port = null, $timestamp = null)
    {
        $ann  = new Annotation();
        if (empty($timestamp)) {
            $ann->timestamp = (microtime(true) * 1000000);
        }
        else {
            $ann->timestamp = (int)$timestamp;
        }

        if (empty($port)){
            $ann->host = EndpointFormat::newDefaultEndpoint($spanName);
        }
        else {
            if (empty($timestamp)) {
                $ann->host = EndpointFormat::newEndpoint($spanName, $port);
            }
            else {
                $ann->host = EndpointFormat::newEndpoint($spanName, $port, intval($timestamp));
            }
        }

        $ann->value = $type;
        return $ann;
    }


}
//
//class AnnotationBuilder
//{
//
//    public static function clientSendAnnotation($spanName)
//    {
//        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['CLIENT_SEND'], $spanName);
//    }
//    public static function  serverReceAnnotation($spanName)
//    {
//        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['SERVER_RECV'], $spanName);
//    }
//    public static function  serverSendAnnotation($spanName)
//    {
//        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'], $spanName);
//    }
//    public static function  clientReceAnnotation($spanName)
//    {
//        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['CLIENT_RECV'], $spanName);
//    }
//
//    //创建新annotation
//    public static function makeAnnotation($type, $spanName, $port = '')
//    {
//        $ann  = new Annotation ();
//        $ann->timestamp = ( microtime(true)*1000000);
//        if (empty($port)){
//            $ann->host = EndpointFormat::newDefaultEndpoint($spanName);
//        }
//        else {
//            $ann->host = EndpointFormat::newEndpoint($spanName, $port);
//        }
//        $ann->value = $type;
//
//        return $ann;
//    }
//
//
//}

//小工厂，专门创建ep
class EndpointFormat {

    public static function newDefaultEndpoint($spanName)
    {
        $epo = new Endpoint();
        $epo->ipv4 = $long = ip2long($_SERVER["REMOTE_ADDR"]);;
        $epo->port = 9091;
        $epo->service_name = $spanName;

        return $epo;
    }

    public static function newEndpoint($spanName,$port)
    {
        $epo = new Endpoint();
        $epo->ipv4 = $long = ip2long($_SERVER["REMOTE_ADDR"]);
        $epo->port = $port;
        $epo->service_name = $spanName;

        return $epo;
    }
}
