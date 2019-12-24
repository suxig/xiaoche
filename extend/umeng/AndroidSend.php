<?php

namespace umeng;

/**
 * 友盟推送（安卓）
 * @author chunkuan<urcn@qq.com>
 */
class AndroidSend
{
    private $appkey;
    private $appMasterSecret;
    private $timestamp;
    
    public function __construct()
    {
        $this->appkey = '5d0352a94ca357b52600087f';
        $this->appMasterSecret = 'gdjqxnygjwcpmcscg72c5scdriqt3bot';
        $this->timestamp = strval(time());
    }
    
    public function unicast($device_token = '', $data = [])
    {
        vendor('Umeng.notification.android.AndroidUnicast');
        $unicast = new \AndroidUnicast();
        $unicast->setAppMasterSecret($this->appMasterSecret);
        $unicast->setPredefinedKeyValue("appkey", $this->appkey);
        $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
        // Set your device tokens here
        $unicast->setPredefinedKeyValue("device_tokens", $device_token); 
        //通知栏提示文字
        $unicast->setPredefinedKeyValue("ticker", $data['ticker']?:'通知栏提示文字');
        //通知栏提示标题
        $unicast->setPredefinedKeyValue("title", $data['title']?:'通知标题');
        //通知文字描述 
        $unicast->setPredefinedKeyValue("text", $data['text']?:'通知文字描述');
        
        $unicast->setPredefinedKeyValue("after_open", "go_app");
        $unicast->setPredefinedKeyValue("production_mode", "true");
        // Set extra fields
        $unicast->setExtraField("content", $data['extra_content']);
        return $unicast->send();
    }
}
