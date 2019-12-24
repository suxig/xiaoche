<?php
namespace app\index\controller;
use think\Db;
class Car extends IndexBase
{

    public function index()
    {
      //连接数据库
        $result1 = $this->getopenid();
        $arr = (array)json_decode( $result1 );
        $a = $arr['openid'];
        $query = "select * from user1 where openid = '$a'";
        $result2 = Db::query( $query);
        if (count($result2) <= 0)
        {
            $query1 = "insert into user1 (openid)  values('$a')";
            $result3=Db::query( $query1);
        }
        $query5 = "DELETE FROM `map` WHERE openid='$a'";
        $result5 = Db::query( $query5);
        $query2 = "insert into map (openid,jing,wei)  values('$a','{$_POST['longitude']}','{$_POST['latitude']}')";
        $result4=Db::query( $query2);
        header("content-type:text/html;charset=utf-8");  
        header('Access-Control-Allow-Origin:*');//解决跨域问题  
        $query = "select * from user1 where openid = '$a'";
        $result2 = Db::query( $query);
        echo json_encode($result2);
    }
    //driver
    public function driver(){
        $openid = $_POST['openid'];
        $bianhao = 0;
        $juli = 100000;
        $road = 0;
        $wz1 = array
        (
            'jing' => 0,
            'wei'=> 0,
            'uptime' => ''
        );
        $wz2 = array
        (
            'jing' => 0,
            'wei'=> 0,
            'uptime' => ''

        );
        //连接数据库
       
        $query1 = "select * from mapd where openid = '$openid'";
        $result1 = Db::query($query1);
        if (mysqli_num_rows($result1) <= 0)
        {
            $query2 = "insert into mapd (openid,jing,wei)  values('$openid','{$_POST['longitude']}','{$_POST['latitude']}')";
            $result2=Db::query($query2);
        }
        else
        {
            //$query3 = "insert into mapd (openid,jing,wei)  values('$openid','{$_POST['longitude']}','{$_POST['latitude']}')";
            $query3 = "UPDATE mapd SET jing = '{$_POST['longitude']}',wei = '{$_POST['latitude']}' WHERE openid = '$openid'";
            $result3=Db::query($query3); 
        }
        $query7 = "insert into driver (openid,jing,wei)  values('$openid','{$_POST['longitude']}','{$_POST['latitude']}')";
        //$result7=Db::query( $query7);
        $query4 = "select * from driver where openid = '$openid' order by uptime desc";
        $result4 = Db::query($query4);
        $aim = 0;
        $row = mysqli_fetch_assoc($result4);
        //从路网一中匹配最近的点
        $query5 = "select * from luwang1";
        $result5 = Db::query($query5);
        while($row2 = mysqli_fetch_array($result5))
        {
            $s = ($row2['jing'] - $row['jing'])*($row2['jing'] - $row['jing'])+($row2['wei'] - $row['wei'])*($row2['wei'] - $row['wei']);
            if($s<$juli)
            {
                $juli = $s;
                $bianhao = $row2['bianhao'];
                $road = 1;
            }
        }
        //从路网二中匹配最近的点$row['wei']
        $query6 = "select * from luwang2";
        $result6 = Db::query( $query6);
        while($row2 = mysqli_fetch_array($result6))
        {
            $s = ($row2['jing'] - $row['jing'])*($row2['jing'] - $row['jing'])+($row2['wei'] - $row['wei'])*($row2['wei'] - $row['wei']);
            if($s<$juli)
            {
                $juli = $s;
                $bianhao = $row2['bianhao'];
                $road = 2;
            }
        }
        //查找last和now
        $query8 = "select * from mapd where openid = '$openid'";
        $result8 = Db::query( $query8);
        $row1 = mysqli_fetch_array($result8);
        $now=$row1['now']; 
        if($row1['last']==null)
        {
            $query9 = "UPDATE mapd SET last='$bianhao',now='$bianhao',xianlu='$road' WHERE openid = '$openid'";
            $result9=Db::query( $query9); 
        }
        elseif($row1['now']!=$bianhao)
        {
            if($row1['now']>$bianhao)
            $aim = 1;
            else 
            $aim = -1;
            $query10 = "UPDATE mapd SET last='$now',now='$bianhao',xianlu='$road',aim='$aim' WHERE openid = '$openid'";
            $result10=Db::query( $query10); 
        }
        echo json_encode($row);
    }
    //getd
    public function getd(){
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
    //change
    public function change(){
        $result = $this->getopenid();
        $arr = (array)json_decode( $result );
        $a = $arr['openid'];
        $query = "select * from user1 where openid = '$a'";
        $result1 = Db::query( $query);
        if (mysqli_num_rows($result1) >= 0)
        {    
            $query1 = "UPDATE user1 SET passenger = 0 WHERE openid = '$a'";
            $result2=Db::query( $query1);
        }
        header("content-type:text/html;charset=utf-8");  
        header('Access-Control-Allow-Origin:*');//解决跨域问题  
        $result = array(  
            'passenger' => 0,   
        );  
        echo json_encode($result);
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
