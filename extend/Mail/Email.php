<?php

namespace Mail;
use phpMailer\PHPMailer;
class Email{

    public function index()
    {
        $date = date("Y-m-d:h:i");//&&isset($_GET["imgpass"])
        $email="geticsen@163.com";//$_GET["email"];
        $account=md5($email.rand(0,999));
        //$password=md5($_GET["password"]);
        $title="恭喜,您已经成功注册了账号！";
        $html='
        <section  style="box-sizing: border-box; background-color: rgb(255, 255, 255); border-radius: 4px; margin-bottom: 10px; box-shadow: rgba(26, 26, 26, 0.1) 0px 1px 3px; padding: 24px; border-top: 4px solid rgb(236, 114, 89);">
            <h3  style="box-sizing: border-box; margin-top: 0px; margin-bottom: 16px; font-weight: 500; text-rendering: optimizelegibility; display: flex; -webkit-box-pack: justify; justify-content: space-between; -webkit-box-align: center; align-items: center; padding-left: 12px; font-size: 18px; height: 20px; line-height: 20px;">
                <p>
                    <br/>
                </p>
                <p>
                    <a style="text-decoration:none;" href="https://www.geticsen.cn">Geticsen博客</a>
                </p>
                <p>
                    <br/>
                </p>
            </h3>
            <p>
                Hello
                <img class="_3nYIo3" src="https://upload.jianshu.io/users/upload_avatars/4170617/d02e25bc-a94a-41dd-9ab3-96964851637c.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/120/h/120/format/webp" alt="  " style="box-sizing: border-box; vertical-align: middle; border: 1px solid rgb(238, 238, 238); min-width: 54px; min-height: 54px; width: 54px; height: 54px; border-radius: 50%;"/>
                '.$email.',感谢您的友链提交，正在等待审核。
                
            </p>
            <h3  style="box-sizing: border-box; margin-top: 0px; margin-bottom: 16px; font-weight: 500; text-rendering: optimizelegibility; display: flex; -webkit-box-pack: justify; justify-content: space-between; -webkit-box-align: center; align-items: center; padding-left: 12px; font-size: 18px; height: 20px; line-height: 20px;">
            2333
            </h3>
        </section>';
        $content="亲爱的".$email."用户您成功开通账户,感谢您的注册测试，我们将继续改进。";
        //pop3 tiucenbmcxtqbifc
        //stmp wjffkoowsvldbgif
        $this->sendEmail("286348794@qq.com","jysqiviqlibabiif","南下.牧风",$email,$title,$html);
        
    }
    public function sendEmail($from,$code,$nickName,$toUser,$title,$content,$attachment=""){
        // 实例化PHPMailer核心类
        $mail = new PHPMailer();
        // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        //$mail->SMTPDebug = 2;
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
        return $status;
    }

}
