<?php
// +----------------------------------------------------------------------
// | s0s0s社区  thinkphp5 版本
// +----------------------------------------------------------------------
// | Copyright (c) 2013~2016 http://s0s0s.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: long <geticsen@163.com>
// +----------------------------------------------------------------------
namespace QQLogin;

class Recorder{
    private static $data;
    private $inc;
    private $error;

    public function __construct(){
        $this->error = new ErrorCase();

        $this->inc = config('QQLoginConfig');
        if(empty($this->inc)){
            $this->error->showError("20001");
        }
        $temp_cache = session('QC_userData');
        if(empty( $temp_cache)){
            self::$data = array();
        }else{
            self::$data = session('QC_userData');
        }
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
        if(empty($this->inc[$name])){
            return null;
        }else{
            return $this->inc[$name];
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    function __destruct(){
        session('QC_userData', self::$data);
    }
}
