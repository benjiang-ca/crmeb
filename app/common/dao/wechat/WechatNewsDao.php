<?php
/**
 *
 * User: Qinii
 * Date: 2020-04-26
 * Time: 11:14
 */
namespace app\common\dao\wechat;

use app\common\dao\BaseDao;
use app\common\model\wechat\WechatNews;

class WechatNewsDao extends BaseDao
{

    protected function getModel(): string
    {
        return WechatNews::class;
    }

    /**
     * 根据主键查询
     * @param int $merId
     * @param int $id
     * @param null $except
     * @return bool
     * @author Qinii
     */
    public function merExists(int $merId, int $id, $except = null)
    {
        return $this->merFieldExists($merId, $this->getPk(), $id, $except);
    }

    /**
     * 根据 字段名查询
     * @param int $merId
     * @param $field
     * @param $value
     * @param null $except
     * @return bool
     * @author Qinii
     */
    public function merFieldExists(int $merId, $field, $value, $except = null)
    {
        return ($this->getModel())::getDB()->when($except, function ($query, $except) use ($field) {
                $query->where($field, '<>', $except);
            })->where('mer_id', $merId)->where($field, $value)->count() > 0;
    }

    public function getAll(array $where)
    {
        if(isset($where['cate_name']) && $where['cate_name'] !== ''){
            $query = WechatNews::hasWhere('article',function ($query)use($where){
               $query->whereLike('title',"%{$where['cate_name']}%");
            });
        }else{
            $query = WechatNews::alias('WechatNews');
        }
        $query->with('article');
        return $query->order('WechatNews.create_time DESC');
    }

    public function get( $id, int $merId = 0)
    {
        return ($this->getModel())::getDB()->where('mer_id',$merId)->with('article.content')->find($id);
    }
}
