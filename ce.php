<?php

/**
 * Class Leb_Plugin_CodeEditor_ce
 *
 * 功能：在任意控制器内调用，会渲染出一个代码编辑器，输入PHP代码，则可以随意执行，就相当于在该控制器方法内动态注入代码
 */
class Leb_plugin_codeEditor_ce{


    public function __construct()
    {
        $root = trim($_SERVER['DOCUMENT_ROOT']);
        if(strpos(PHP_OS, 'WIN') !== false){
            $root = str_replace('/', '\\', $root);
        }

        !defined("_ROOT_") && define("_ROOT_", $root);
    }

    /**
     * 展示代码编辑器
     */
    public function show()
    {
        //获取本方法在何处被调用
        if ($trace = debug_backtrace()) {
            if ($trace < 2)
                throw new Exception('调用方式有误');
        } else {
            throw new Exception('调用方式有误');
        }

        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $result = '';

            $codeStr = join("\n", $_POST['code']);
            $codeStr = preg_replace('/(<\?php)|(\?>)/', '', $codeStr);

            setcookie('runcode', $codeStr, time() + 3600 * 24 * 30, '/', $this->_getDomain($_SERVER['HTTP_HOST']));

            $result = $this->_generateResult($codeStr);
            echo $result;exit;
        }else {
            $dir_prefix = DIRECTORY_SEPARATOR.trim(str_replace(_ROOT_, '', dirname(__FILE__)), DIRECTORY_SEPARATOR);

            $defaultCode = $_COOKIE['runcode'] ?? '';

            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'editor.php');
        }
    }

    /**
     * @param string $code
     */
    private function _generateResult($codeStr='')
    {
        ob_start();

        echo '服务器IP： '.$_SERVER['SERVER_ADDR']."<br/>";
        echo "<hr/>";
        try {
            eval($codeStr);
            $result = ob_get_contents();
        }catch (Exception $e){
            $result = $e->getMessage();
        }

        ob_end_clean();

        $result = str_replace("\n", '<br/>', $result);
        return $result;
    }

     //获取根域名
    private function _getDomain($url)
    {
        if(substr($url, 0, 4) == 'http') {
            $rs = parse_url($url);
            $host = $rs['host'];
        }elseif($index = strpos($url, '/')) {
            $host = substr($url, 0, $index);
        }else{
            $host = $url;
        }
        $arr = explode('.', $host);
        $last = array_pop($arr);
        $map = array('com','net','org','gov','cc','biz','info');
        $last2 = array_pop($arr);
        if(in_array($last2, $map)) {
            $last3 = array_pop($arr);
            $domain = $last3.'.'.$last2.'.'.$last;
        }else{
            $domain = $last2.'.'.$last;
        }
        return $domain;
    }
}
?>