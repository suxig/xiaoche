<?php
namespace app\index\model;
use think\Model;
class Article extends Model
{
    protected $pk = 'id';
    public function getArticleIsPublishAttr($value)
    {
        $status = [0=>'未发布',1=>'已发布'];
        return $status[$value];
    }
    public function getArticleLastUpdateAttr($value)
    {
        return explode(" ",$value)[0];
    }
    public function getArticleCreateTimeAttr($value)
    {
        return explode(" ",$value)[0];
    }
    public function getArticleType($value)
    {

        $status = [ 1	=>	"iOS",
                    2	=>	"算法",
                    3	=>	"人工智能",
                    4	=>	"安全",
                    5	=>	"运维",
                    6	=>  "前端",
                    7	=>	"后端",
                    8	=>	"Android",
                    9	=>	"产品运营",
                    10	=>	"硬件开发",
                    11	=>	"测试",
                    12	=>	"大数据",
                    13	=>	"数据库管理",
                    14	=>	"设计",
                    15	=>	"其他",
                    16	=>	"哲学",
                    17	=>	"小说",
                    18	=>	"名著",
                    19	=>	"社会",
                    20	=>	"情感",
                    21	=>	"风俗",
                    24	=>	"地理",
                    23	=>	"天文",
                    22	=>	"社科",
                    25	=>	"数学",
                    26	=>	"物理",
                    27	=>	"化学",
                    28	=>	"生物",
                    29	=>	"宇宙史",
                    30	=>	"地球史",
                    31	=>	"生命史",
                    32	=>	"人类史",
                    33	=>	"社会史",
                    34	=>	"国家史",
                    35	=>	"技术史",
                    36	=>	"发展史"];
        return $status[$value];
    }
    
    
}