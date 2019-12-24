<?php

namespace xunfei;

/**
 * 讯飞语音合成
 */
class Xfyun
{
    private $apiPath;
    private $appId;
    private $appKey;
    
    public function __construct()
    {
        $this->apiPath = 'http://api.xfyun.cn/v1/service/v1/tts';
        $this->appId = '5d09879c';
        $this->appKey = '1f3fda9c5fb1d1564f8c7bcd4096e968';
    }
    
    function tocurl($url, $header, $content)
    {
        $ch = curl_init();
        if (substr($url, 0, 5) == 'https')
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
        $response = curl_exec($ch);
        $error = curl_error($ch);
        //var_dump($error);
        if ($error)
        {
            die($error);
        }
        $header = curl_getinfo($ch);

        curl_close($ch);
        $data = array('header' => $header, 'body' => $response);
        return $data;
    }

    function hecheng($content = '')
    {
        // 合成webapi接口地址
        $url = $this->apiPath;
        // 应用ID 必须为webapi类型应用，并开通语音合成服务,参考帖子如何创建一个webapi应用 http://bbs.xfyun.cn/forum.php?mod=viewthread&tid=36481
        $appid = $this->appId;
        // 接口密钥 （webapi类型应用开通合成服务后，控制台--我的应用---语音合成---相应服务的apikey）
        $apikey = $this->appKey;
        // 音频编码（raw合成的音频格式pcm、wav,lame合成的音频格式MP3）发音人（控制台应用对应服务添加发音人之后查看参数）
        $param = array(
            'aue' => 'lame',
            'voice_name' => 'xiaoyan',
        );
        // 组装http请求头
        $time = date('Y-m-d-H-i-s');
        $xparam = base64_encode(json_encode(($param)));
        $checksum = md5($apikey . time() . $xparam);
        $header = array(
            'X-CurTime:' . time(),
            'X-Param:' . $xparam,
            'X-Appid:' . $appid,
            'X-CheckSum:' . $checksum,
            'X-Real-Ip:127.0.0.1',
            'Content-Type:application/x-www-form-urlencoded; charset=utf-8'
        );
        $content = array(
            'text' => $content, //'Z003号机器存在故障，请检修', // webapi是单次只支持1000个字节，具体看您的编码格式，计算一下具体支持多少文字）
        );
        $response = $this->tocurl($url, $header, $content);
        $header = $response['header'];
        $filename = 'upload/voices/'.date('Y/md').'/';
        mkdir($filename, 0777, true);
        if ($header['content_type'] == 'audio/mpeg')
        {
            $filename .= uniqid() . '.mp3';
            $res = file_put_contents($filename, $response['body']);
        } else
        {
            $filename .= uniqid() . '.wav';
            $res = file_put_contents($filename, $response['body']);
        }
        
        return $filename;
    }

}
