<?php
use Slim\Slim;

/**
 * @desc 常用函数的文件
 * @author macro chen  <macro_fengye@163.com>
 * @date 2015/07/07
 */

/**
 * 动态的添加应该包含的路由表
 * @param \Slim\Slim $app
 * @return string
 * {{{
 */
function requireRouteFile($app)
{
    $path_info = $app->request->getPathInfo();
    $file = explode("/", $path_info)[1];
    if (empty($file) && strcmp($path_info, "/") === 0) {
        $file = "home";
    }
    return $file;
}

//}}}

//以下为常用中间件{{{
function mw1()
{
    echo "This is middleware!<br/>";
}

function mw2()
{
    echo "This is middleware!<br/>";
}

//关于管理员的中间件
function check_login()
{
    echo "您还没登录，不是操作其他的数据...";
    return false;
}

//}}}

/**
 * 字节转换
 * @param $size
 * @return string
 */
function convert($size)
{
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

/**
 * URL参数安全base64
 *
 * @author macro chen <macro_fengye@163.com>
 * @param string $string
 * @param string $operation ENCODE|DECODE
 * @return string
 */
function urlSafeBase64Code($string, $operation = 'ENCODE')
{
    $searchKws = array('+', '/', '=');
    $replaceKws = array('_', '-', '');
    $ret = '';
    if ($operation == 'DECODE') {
        $ret = base64_decode(str_replace($replaceKws, $searchKws, $string));
    } else {
        $ret = str_replace($searchKws, $replaceKws, base64_encode($string));
    }
    return $ret;
}

/**
 * discuz算法数据加解密
 *
 * @author macro chen <macro_fengye@163.com>
 * @param string $string 加解密字符串
 * @param string $key 密钥
 * @param string $operation 加密或解密操作 ENCODE|DECODE
 * @param integer $expiry 加密字符串过期时间
 * @return string
 */
function authcode($string, $key, $operation = 'DECODE', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ?
        urlSafeBase64Code(substr($string, $ckey_length), 'DECODE')
        : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . urlSafeBase64Code($result, 'ENCODE');
    }
}

/**
 * 错误处理
 * @param $request
 * @param $response
 * @param $exception
 */
function errorHandler($request, $response, $exception)
{
}

/**
 * @param $request
 * @param $response
 * @param $allowedHttpMethods
 */
function notAllowedHandler($request, $response, $allowedHttpMethods)
{
    echo "nnnnn";
}

/**
 * @param $request
 * @param $response
 */
function notFound($request, $response)
{
    echo __FUNCTION__;
}

function fatal_handler()
{
    if (!is_null(error_get_last())) {
        echo('There is a fatal error!');
    }
}
