<?php
namespace app\index\controller;
use think\Db;
class Getd extends IndexBase
{

   
    
    //getd
    public function index(){
        $aim = 0;
        $road = 0;
        $juli = 100000;
        $op = "no";
        //获取目的地坐标
        $query3 = "select * from aim where go='{$_POST['aim']}'";
        $result3 = Db::query($query3);
        $row1 = mysqli_fetch_array($result3);
        $road = $row1['xianlu'];
        //获取用户坐标
        $query4 = "select * from map where openid='{$_POST['openid']}'";
        $result4 = Db::query($query4);
        $row2 = mysqli_fetch_array($result4);
        $aim = bccomp($row1['wei'],$row2['wei'],7);
        $wei = $row2['wei'];
        $jing = $_POST['longitude'];
        $wei = $_POST['latitude'];
        //更新乘客方向和路线
        if($aim == 1)
        {
            $query5 = "UPDATE map SET aim = '1',road = '$road',jing = '$jing',wei = '$wei' WHERE openid = '{$_POST['openid']}' ";
            $result5 = Db::query($query5);
        }
        elseif($aim==-1)
        {
            $query6 = "UPDATE map SET aim = '-1',road = '$road',jing = '$jing',wei = '$wei' WHERE openid = '{$_POST['openid']}' ";
            $result6 = Db::query($query6);
        }
        else
        {
            $query7 = "UPDATE map SET aim = '0',road = '$road',jing = '$jing',wei = '$wei' WHERE openid = '{$_POST['openid']}' ";
            $result7 = Db::query($query7);
        }
        //筛选最优司机
        header("content-type:text/html;charset=utf-8");
        header('Access-Control-Allow-Origin:*');
        if($aim == '1')
            $query1 = "select * from mapd where aim=1 and wei<'$wei' and road = '$road'";
        elseif($aim == "-1")
            $query1 = "select * from mapd where aim=-1 and wei>'$wei' and road = '$road'";
        $result1 = Db::query($query1);
        $t=mysqli_num_rows($result1); 
        if (mysqli_num_rows($result1) <= 0)
        {
            $query2 = "select * from mapd where aim='-$aim' and road = '$road'";
            $result2 = Db::query($query2);
            $row3['jing'] = 0.0000000001;
            $row3['wei'] = 0.0000000001;
            if($aim == 1)
            {
                $row3['jing'] = 112.9166811;
                $row3['wei'] = 27.9004213;
            }
            elseif($aim == -1)
            {
                if($road == 1)
                {
                    $row3['jing'] = 112.9065846;
                    $row3['wei'] = 27.9146352;
                }
                elseif($road == 2)
                {
                    $row3['jing'] = 112.9127743;
                    $row3['wei'] = 27.9133777;
                }
            }
            while($row = mysqli_fetch_array($result2))
            {
                $s = ($row3['jing'] - $row['jing'])*($row3['jing'] - $row['jing'])+($row3['wei'] - $row['wei'])*($row3['wei'] - $row['wei']);
                if($s<$juli)
                {
                    $juli = $s;
                    $op = $row['openid'];
                }
            }
            $query10 = "select * from mapd where openid = '$op'";
            $result10 = Db::query($query10);
            $row = mysqli_fetch_array($result10);
        }
        else
        {
            while($row = mysqli_fetch_array($result1))
            {
                $s = ($row2['jing'] - $row['jing'])*($row2['jing'] - $row['jing'])+($row2['wei'] - $row['wei'])*($row2['wei'] - $row['wei']);
                if($s<$juli)
                {
                    $juli = $s;
                    $op = $row['openid'];
                }
            }
            $query9 = "select * from mapd where openid = '$op'";
            $result9 = Db::query($query9);
            $row = mysqli_fetch_array($result9);
        }
        //$row['wei'] = $row['wei']-0.00169;
        //$row['jing'] = $row['jing']+0.00116;
        echo json_encode($t);
    }
   
    //获取id
    private function getopenid(){
        $js_code = $_POST['code'];
        $appid = 'wxe7fdf205417d46a7';
        $appsecret = 'd35742db586a7bbc5dd11a6862e506b5';
        $curl = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
        $curl = sprintf($curl,$appid,$appsecret,$js_code);
        $data = array(
            'openid'=>'OPENID',
            'session_key'=> 'SESSIONKEY',
            'unionid'=> 'UNIONID'
        );
            $data = @http_build_query($data);  //把参数转换成URL数据  
        
            $aContext = array('http' => array('method' => 'POST',  
        
                                                                        'header'  => 'Content-type: application/x-www-form-urlencoded',  
        
                                                                        'content' => $data ));  
        
            $cxContext  = stream_context_create($aContext);  
        
            $sUrl = $curl; //此处必须为完整路径  
        
            $d = @file_get_contents($sUrl,false,$cxContext);   
        return $d;
    }
    
  
}
