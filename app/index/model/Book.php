<?php
namespace app\index\model;
use think\Model;
class Book extends Model
{
    protected $pk = 'book_id';
    public function getBookIsPublishAttr($value)
    {
        $status = [0=>'未发布',1=>'已发布'];
        return $status[$value];
    }
}