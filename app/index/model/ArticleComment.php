<?php
namespace app\index\model;
use think\Model;
class ArticleComment extends Model
{
    
    public function getCommentStatusAttr($value)
    {
        $status = [0=>'未通过',1=>'已通过',2=>'已删除'];
        return $status[$value];
    }
    public function getCommentCreateTimeAttr($value)
    {
        return explode(" ",$value)[0];
    }
}