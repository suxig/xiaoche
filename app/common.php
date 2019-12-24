<?php
// 应用公共文件
use think\Session;
use think\Cookie;
use phpMailer\PHPMailer;
use phpMailer\Exception;
//阿里云
use OSS\OssClient;
use OSS\Core\OssException;
//验证码
use Gif\GIFGenerator;
/**
 * 文件大小转换
 */
function transByte($byte)
{
    $KB = 1024;
    $MB = 1024 * $KB;
    $GB = 1024 * $MB;
    $TB = 1024 * $GB;
    if ($byte < $KB) {
        return $byte . "B";
    } elseif ($byte < $MB) {
        return round($byte / $KB, 2) . "KB";
    } elseif ($byte < $GB) {
        return round($byte / $MB, 2) . "MB";
    } elseif ($byte < $TB) {
        return round($byte / $GB, 2) . "GB";
    }else {
        return round($byte / $TB, 2) . "TB";
    }
}
/**
 * 计算文件夹大小（含内部文件）
 */
function ListSize($dir,$all=false){
    $size = 0;
    if(is_dir($dir)){
        $dirArr = scandir($dir);
        foreach($dirArr as $v){
            if($v!="." && $v!=".."){
               
                $dirname = $dir.DIRECTORY_SEPARATOR.$v;  //子文件夹的目录地址 
                //是否是文件夹
                if(is_dir($dirname)){
                    if($all){
                     $size += ListSize($dirname,$all);
                    }
                    
                }else{
                     $size += filesize($dirname);
                }
            }
        }
    }else{
        $size += filesize($dir);
    }
    return $size;
}
/**
 * 生成验证码
 */
function createCheckCode()
{
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        // Output as a GIF image
		ob_clean();
        header ('Content-type:image/gif');
        // Initialize a new GIFGenerator object
        $gif = new GIFGenerator();
        // Create a multidimensional array with all the image frames
        $num=[];
        $ans=0;
        for($i=0;$i<5;$i++){
            $num[$i]=rand(0,5);
            $ans+=$num[$i];
        }
        $font=[
            0=>"AxureHandwriting-Bold.otf",
            1=>"AxureHandwriting-Italic.otf",
            2=>"Lato-Light.ttf",
            3=>"AllCaps.ttf",
            4=>"Dani.ttf",
            5=>"daniel.ttf",
            6=>"KidsFirstPrintFont.ttf",
            7=>"VIVACS.TTF",
            8=>"P22Royalist.ttf"
        ];
        $color=['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
        $imageFrames = array(
            'repeat' => true,
            'frames' => array(
                array(
                    'image' => './images/temp.jpg',
                    'text' => array(
                        array(
                            'text' => $num[0].'+'.$num[1].'+'.$num[2],
                            'fonts' => './fonts/'.$font[rand(0,count( $font)-1)],
                            'fonts-size' => 30,
                            'angle' => 0,
                            'fonts-color' => '#'.$color[rand(0,15)].$color[rand(0,15)].$color[rand(0,15)],
                            'x-position' =>50+rand(-50,50),
                            'y-position' =>50+rand(-10,10)
                        )
                    ),
                    'delay' => 100+rand(-50,50)
                ),
                array(
                    'image' => './images/temp.jpg',
                    'text' => array(
                        array(
                            'text' => '+'.$num[3].'+'.$num[4].'=? ',
                            'fonts' => './fonts/'.$font[rand(0,count( $font)-1)],
                            'fonts-size' =>30,
                            'angle' => 0,
                            'fonts-color' => '#'.$color[rand(0,15)].$color[rand(0,15)].$color[rand(0,15)],
                            'x-position' => 50+rand(-50,50),
                            'y-position' => 50+rand(-10,10)
                        )
                    ),
                    'delay' => 100+rand(-50,50)
                )
            )
        );
        echo $gif->generate($imageFrames);
        Session::set('checkCodeAnswer',$ans);
}
/**
 * 获取验证码的结果
 */
function getCheckCodeAnswer(){
    return Session::get('checkCodeAnswer');
}
/**
 * Oss上传
 */
function uploadfileToOss($files,$bucket="geticsen-cn"){
    
    // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
    $oss_config = config('oss_config');
    $accessKeyId = $oss_config["accessKeyId"];
    $accessKeySecret = $oss_config["accessKeySecret"];
    // Endpoint以杭州为例，其它Region请按实际情况填写。
    $endpoint = $oss_config["endpoint"];
    // 存储空间名称
    
    //$bucket= $oss_config['bucket'];
    // 文件名称
    $object =[];
    $filePath = [];
    // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
    //$filePath = "C:\\Users\\ZEC--\\Desktop\\QQ图片20181119115842.jpg";
    try{
        
        $ossClient = new OssClient($accessKeyId,$accessKeySecret, $endpoint);
        if(is_array($files)){
             foreach($files as $file){
                $ossClient->uploadFile($bucket,$file ,$file );
             }
        }else{
           $ossClient->uploadFile($bucket, $files, $files);
        }

    }catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
}
function ListOssFiles($bucket="geticsen-cn",$prefix="",$delimiter="",$nextMarker="",$maxkeys="100"){
    $oss_config = config('oss_config');
    $accessKeyId = $oss_config["accessKeyId"];
    $accessKeySecret = $oss_config["accessKeySecret"];
    // Endpoint以杭州为例，其它Region请按实际情况填写。
    $endpoint = $oss_config["endpoint"];
    $prefix = 'images/';
    $delimiter = '/';
    $nextMarker = '';
    $maxkeys = 10;
    $options = array(
        'delimiter' => $delimiter,
        'prefix' => $prefix,
        'max-keys' => $maxkeys,
        'marker' => $nextMarker,
    );
    try {
        $ossClient = new OssClient($accessKeyId,$accessKeySecret, $endpoint);
        $listObjectInfo = $ossClient->listObjects($bucket, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $objectList = $listObjectInfo->getObjectList(); // object list
    $prefixList = $listObjectInfo->getPrefixList(); // directory list
    
    if (!empty($objectList)) {
        print("objectList:\n");
        foreach ($objectList as $objectInfo) {
            print($objectInfo->getKey() . "<br/>");
        }
    }
    if (!empty($prefixList)) {
        print("prefixList: \n");
        foreach ($prefixList as $prefixInfo) {
            print($prefixInfo->getPrefix() . "<br/>");
        }
    }
    return array($objectList,$prefixList);
}
/**
 * 用户登录
 */
/*
"userList"=>[
    "user_name"=>"geticsen",
    "user_head_url"=>"http://www.encai.online/static/static/img/face.jpg",
    "user_home_url"=>"/User/user_home/geticsen",
    "user_authority" =>"admin",
    "user_last_login_time"=>"",
    "rand_pass"     =>"",
    "account"       =>"geticsen",
    "user_id"       =>""
]
*/
function setUserLogin($userList=""){
    $randPassword = md5(rand(0,9999));
    $loginList=[
        "isLogin"=>true,
        "userList"=>$userList,
        "tempPassword"=>md5($userList['account'].$randPassword)
    ];
    

    $loginList_cookie=[
        "randPassword"=>$randPassword,
        "account"     =>$userList["account"]
    ];
    
    Session::set('loginList',$loginList);
    Cookie::set('loginList',$loginList_cookie,3600*24);
}
/**
 * 获取用户登录信息
 */
function getUserLoginMessage(){
    $loginList = Session::get('loginList');
    $loginList_cookie = Cookie::get('loginList');
    if($loginList!=""&&$loginList_cookie!=""){
        if($loginList["tempPassword"]==md5($loginList_cookie["account"].$loginList_cookie["randPassword"])){
            return $loginList;
        }else{
            $loginList=array("isLogin"=>false);
            return $loginList;
        }
    }else{
        $loginList=array("isLogin"=>false);
        return $loginList;
    }
}
/**
 * 用户登录注销
 */
function setUserOut(){
    Session::set('loginList',"");
    Cookie::set('loginList',"",0);
}
/**
 * 获取设备类型
 */
function getDeviceType(){
      //get  USER AGENT
      $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	  //get info
	  //echo $agent;
	  $postmanruntime =  false ;
      $is_pc = (strpos($agent, 'windows nt')) ? true : false;
      $is_mac = (strpos($agent, 'Macintosh')) ? true : false;
      $is_iphone = (strpos($agent, 'iphone')) ? true : false;
      $is_ipad = (strpos($agent, 'ipad')) ? true : false;
      $is_android = (strpos($agent, 'android')) ? true : false;
      //telephone
    if($is_pc||$is_ipad||$is_mac||$postmanruntime){
          return "PC";
    }else if($is_iphone||$is_android){
          return "MB";
    }else{
          return "OTHER";
    }
}
/**
 * 返回数据接口
 */
function resultBackJson($status,$msg,$data){
    $callback = isset($_GET["callback"])?$_GET["callback"]:"";
	$result = array(
		'status'   => $status,
		'msg'      => $msg,
		'data'     => $data,
	 );
	if($callback==""){
		 echo  json_encode($result);
	}else{
		 echo  $callback . '(' .json_encode($result). ')'; 
	}
}
/**
 * 
 * 授权认证
 */
function isAuthority(){
      
       $is_author=false;
    
       $s=Session::get("Sauthor");
       $c=Cookie::get("Cauthor");
       //$k=Session::get("Skey");
      if($s==$c&&($s!=""&&$c!="")){
          $is_author=true;
      }


      return  $is_author;
}
/**
 *
 * 是否登录
 */
function isLogin(){
	  $isLogin = false;
	  $tempPassword = Session::get('tempPassword');
      //密匙有效期24分钟
	  $account = Cookie::get('account');
	  $randPassword = Cookie::get('randPassword');
	  if($tempPassword!=""&&$account!=""&&$randPassword!=""){
		if($tempPassword==md5($account.$randPassword)){
			$isLogin = true;
		}
	  }
	  return $isLogin;
}

function backArray($imgs){
	$image = array_values(array_unique (array_diff (explode ("&",$imgs), array (""))));
	return $image;
}
function backImgNum($imgs){
	$image = array_values(array_unique (array_diff (explode ("&",$imgs), array (""))));
	return count($image);
}
function backImg($image,$id){
	if(strpos($image[$id],"uploads")){
		
		return 'https://'.config("oss_config")['bucket'].".oss-cn-shanghai.aliyuncs.com/".substr($image[$id],strpos($image[$id],"uploads"));
	}else{
		return $image[$id];
	}
    
}

function backDate($Date){
	$date=explode(" ",$Date);
	return $date[0];
}
function backMothDay($Date){
	$date=explode("-",explode(" ",$Date)[0]);
	return $date[1]."-". $date[2];
}
/***
 * 发送邮件
 * 创建时间：2019.3.7
 * 创建人：geticsen
 * 
 */
function sendEmail($from,$code,$nickName,$toUser,$title,$content,$attachment=""){
    // 实例化PHPMailer核心类
    $mail = new PHPMailer();
    // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
    //$mail->SMTPDebug = 1;
    // 使用smtp鉴权方式发送邮件
    $mail->isSMTP();
    // smtp需要鉴权 这个必须是true
    $mail->SMTPAuth = true;
    // 链接qq域名邮箱的服务器地址
    $mail->Host = 'smtp.qq.com';
    // 设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = 'ssl';
    // 设置ssl连接smtp服务器的远程服务器端口号
    $mail->Port = 465;
    // 设置发送的邮件的编码
    $mail->CharSet = 'UTF-8';
    // 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = $nickName;
    // smtp登录的账号 QQ邮箱即可
    $mail->Username = $from;
    // smtp登录的密码 使用生成的授权码
    $mail->Password = $code;
    // 设置发件人邮箱地址 同登录账号
    $mail->From = $from;
    // 邮件正文是否为html编码 注意此处是一个方法
    $mail->isHTML(true);
    // 设置收件人邮箱地址
    $mail->addAddress($toUser);
    // 添加多个收件人 则多次调用方法即可
    //$mail->addAddress('1931946508@qq.com');
    // 添加该邮件的主题
    $mail->Subject = $title;
    // 添加邮件正文
    $mail->Body = $content;
    // 为该邮件添加附件
    if($attachment!=""){
    $mail->addAttachment($attachment);
    }
    // 发送邮件 返回状态
    $status = $mail->send();
   // echo $status;
}
/***
 * 返回数字对应的excel表格的列称
 * 
 */

function get_excel_col_name($col=1){
    $col_name="";
    if($col<=0){
        return "index out of range";
    }
    do {
         $col--;
         $col_name = chr(($col %26)+65) .$col_name;
         $col = floor (($col - ($col % 26)) / 26);
     } while ($col > 0);

    return $col_name;
}

/**
* 下载Excel
*/
function download_excel($title,$start_date,$end_date,$data,$head=2,$merge=""){
    $excel = new \excel\Export();    
    $excel->data($data);
    $sheet = $excel->objPHPExcel->getActiveSheet();
    //合并
    //列的数目
    $cols_num=count($data[$head]);
    for($i=1;$i<=$head;$i++){
        $sheet->mergeCells('A'.$i.':'.get_excel_col_name($cols_num).$i);
    }
    //如果有合并
    if($merge!=""){
        for($i=0;$i<count($merge);$i++){
            $sheet->mergeCells($merge[$i]);
        }
    }
    //设置默认行高
    $sheet->getDefaultRowDimension()->setRowHeight(20);
    //第一行设置为30px高
    $sheet->getRowDimension('1')->setRowHeight(30);
    //其他行设置为23
    for($i=2;$i<=$head+1;$i++){
        $sheet->getRowDimension("".$i)->setRowHeight(23);
    }
	
    //列宽设置为20
	for($i=1;$i<=$cols_num;$i++){
            //根据数字获取到列的名字然后设置列的宽度
			$sheet->getColumnDimension(get_excel_col_name($i))->setWidth(20);
	}

	//设置第一行、第二行的居中属性
	//全部居中
    $sheet->getStyle('A1:'.get_excel_col_name($cols_num).count($data))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
    $sheet->getStyle('A1:'.get_excel_col_name($cols_num).count($data))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
	//设置表头背景色
	$sheet->getStyle('A'.($head+1).':'.get_excel_col_name($cols_num).($head+1))->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
	$sheet->getStyle('A'.($head+1).':'.get_excel_col_name($cols_num).($head+1))->getFill()->getStartColor()->setARGB('FFE6E6E6');
			
	//设置表头及其以下的边框
	$styleArray = [
		'borders' => [
			'allborders' => [
				'style' => \PHPExcel_Style_Border::BORDER_THIN
			]
		]
	];
	$sheet->getStyle('A'.($head+1).':'.get_excel_col_name($cols_num) . count($data))->applyFromArray($styleArray);
			
    $excel->save($title . ($start_date!=''?$start_date .'至'. $end_date:''));
}

/**
* 用户PDF报表下载
*/
function pdf($name = '', $html = '', $filename = '') {
		set_time_limit(0);
        if ($name && $html) {
            session('pdf_data', [
                'name' => $name,
                'html' => $html,
                'filename' => $filename
            ]);
            $this->success('save ok.');
        } else {
            $pdf_data = session('pdf_data');
            if ($pdf_data !== null) {
                $pdf = new \tcpdf\Page($pdf_data['name']);
                $pdf->html($pdf_data['html']);
                $pdf->down($pdf_data['filename'] . '.pdf');
            }
        }
}