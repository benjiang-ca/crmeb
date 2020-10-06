<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <div class="container">
          <div class="goodsTitle acea-row">
            <div class="title">文章信息</div>
          </div>
        </div>
      </div>
      <div>
        <span class="spBlock spTitle mb10">商城首页</span>
        <span class="spBlock">地址：<i>/pages/index/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">购物车</span>
        <span class="spBlock">地址：<i>/pages/order_addcart/order_addcart</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">个人中心</span>
        <span class="spBlock">地址：<i>/pages/user/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">商品列表</span>
        <span class="spBlock">地址：<i>/pages/columnGoods/goods_list/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">商品详情</span>
        <span class="spBlock mb5">地址：<i>/pages/goods_details/index</i></span>
        <span class="spBlock mb5">参数：<i>id：产品id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/goods_details/index?id=75</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">产品分类</span>
        <span class="spBlock">地址：<i>/pages/goods_cate/goods_cate</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">找回密码</span>
        <span class="spBlock">地址：<i> pages/retrieve_password/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">客服列表</span>
        <span class="spBlock mb5">地址：<i>/pages/customer_list/index</i></span>
        <span class="spBlock mb5">参数：<i>type:0为用户 1为客服</i></span>
        <span class="spBlock spEx">例如：<i>/pages/customer_list/index?type=0</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">客服用户聊天</span>
        <span class="spBlock mb5">地址：<i>/pages/customer_list/chat</i></span>
        <span class="spBlock mb5">参数：<i>mer_id:商铺id；产品id；订单id；refund_order_id：退款id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/customer_list/chat?mer_id=2&productId=12&orderId=4&refund_order_id=5</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">客服客服聊天</span>
        <span class="spBlock mb5">地址：<i>/pages/customer_list/chat</i></span>
        <span class="spBlock mb5">参数：<i>mer_id:商铺id；客服id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/customer_list/chat?userId=7238&mer_id=10</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">资讯</span>
        <span class="spBlock">地址：<i>/pages/news_list/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">资讯详情</span>
        <span class="spBlock mb5">地址：<i>/pages/news_details/index</i></span>
        <span class="spBlock mb5">参数：<i>id：新闻id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/news_details/index?id=51</i></span>
        <el-divider />
      </div>

      <div>
        <span class="spBlock spTitle mb10">CRMEB</span>
        <span class="spBlock">地址：<i>/pages/auth/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">搜索商品</span>
        <span class="spBlock">地址：<i>/pages/columnGoods/goods_search/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">搜索商品列表</span>
        <span class="spBlock mb5">地址：<i>/pages/columnGoods/goods_search_con/index</i></span>
        <span class="spBlock mb5">参数：<i>searchValue：搜索的参数</i></span>
        <span class="spBlock spEx">例如：<i>/pages/columnGoods/goods_search_con/index?searchValue=%20三生三世</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">支付状态</span>
        <span class="spBlock">地址：<i>/pages/order_pay_status/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单详情</span>
        <span class="spBlock mb5">地址：<i>/pages/order_details/index</i></span>
        <span class="spBlock mb5">参数：<i>order_id</i>：订单号</span>
        <span class="spBlock spEx">例如：<i>/pages/order_details/stay?order_id=115</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">待付款订单详情</span>
        <span class="spBlock mb5">地址：<i>/pages/order_details/stay</i></span>
        <span class="spBlock mb5">参数：<i>order_id：订单号</i></span>
        <span class="spBlock spEx">例如：<i>/pages/order_details/stay?order_id=115</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">退款选择服务类型</span>
        <span class="spBlock mb5">地址：<i>/pages/users/refund/select</i></span>
        <span class="spBlock mb5">参数：<i>order_id：订单号 type：2为批量退款 1为退款</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/refund/select?order_id=63&type=2</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">批量退款</span>
        <span class="spBlock mb5">地址：<i>/pages/users/refund/index</i></span>
        <span class="spBlock mb5">参数：<i> order_id：订单号,  type：2为批量退款 1为退款,  refund_type：1为退款 2为退货退款</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/refund/index?order_id=63&refund_type=2&type=2</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">申请退款</span>
        <span class="spBlock mb5">地址：<i>/users/refund/confirm</i></span>
        <span class="spBlock mb5">参数：<i> order_id：订单号,  type：2为批量退款 1为退款,  refund_type：1为退款 2为退货退款,  ids：商品id</i></span>
        <span class="spBlock spEx">例如：<i>/users/refund/confirm?ids=65&refund_type=1&type=2&order_id=63</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">忘记密码</span>
        <span class="spBlock">地址：<i>/pages/users/retrievePassword/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">个人资料</span>
        <span class="spBlock">地址：<i>/pages/users/user\_info/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">我的优惠券</span>
        <span class="spBlock">地址：<i>/pages/users/user_coupon/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">收藏商品</span>
        <span class="spBlock">地址：<i>/pages/users/user\_goods\_collection/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">关注店铺</span>
        <span class="spBlock">地址：<i>/pages/users/user_store_attention/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">浏览记录</span>
        <span class="spBlock">地址：<i>/pages/users/browsingHistory/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">我的账户</span>
        <span class="spBlock">地址：<i> /pages/users/user_money/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">账单明细</span>
        <span class="spBlock mb5">地址：<i>/pages/users/user_bill/index</i></span>
        <span class="spBlock mb5">参数：<i>type：0为账单记录、1为消费记录、2为充值记录</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/user_bill/index?type=1</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">我的推广</span>
        <span class="spBlock">地址：<i>/pages/users/user_spread_user/index</i></span>
        <el-divider />
      </div>
      <div>
        <span class="spBlock spTitle mb10">分销海报</span>
        <span class="spBlock">地址：<i>/pages/users/user_spread_code/index</i></span>
        <el-divider />
      </div>
      <div>
        <!--eslint-disable-->
        <span class="spBlock spTitle mb10">提现记录 、佣金明细</span>
        <span class="spBlock mb5">地址：<i>/pages/users/user_spread_money/index</i></span>
        <span class="spBlock mb5">参数：<i>type：1为提现记录，2为佣金明细</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/user_spread_money/index?type=1</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">提现</span>
        <span class="spBlock">地址：<i>/pages/users/user_cash/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">添加收货地址</span>
        <span class="spBlock mb5">地址：<i>/pages/users/user_address/index</i></span>
        <span class="spBlock mb5">参数：<i>cartId：地址编号；无id时为新增地址;</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/user_address/index?cartId=5683&pinkId=0&couponId=0</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">地址管理</span>
        <span class="spBlock mb5">地址：<i>/pages/users/user_address_list/index</i></span>
        <span class="spBlock mb5">参数：<i>cartId：地址编号；无id时为新增地址;pinkId:拼团id；couponId：优惠券id；</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/user_address_list/index?cartId=5683</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">绑定手机号</span>
        <span class="spBlock">地址：<i>/pages/users/user_phone/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">余额充值</span>
        <span class="spBlock">地址：<i>/pages/users/user_payment/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">修改密码</span>
        <span class="spBlock">地址：<i>/pages/users/user_pwd_edit/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">提交订单</span>
        <span class="spBlock mb5">地址：<i>/pages/users/order_confirm/index</i></span>
        <span class="spBlock mb5">参数：<i>cartId：购物车编号</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/order\_confirm/index?cartId=853,879</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">推广人订单</span>
        <span class="spBlock">地址：<i>/pages/users/promoter-order/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">推广人列表</span>
        <span class="spBlock">地址：<i>/pages/users/promoter-list/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">推广人排行</span>
        <span class="spBlock">地址：<i>/pages/users/promoter_rank/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">佣金排行</span>
        <span class="spBlock">地址：<i>/pages/users/commission_rank/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">我的订单</span>
        <span class="spBlock mb5">地址：<i>/pages/users/order\list/index</i></span>
        <!--eslint-disable-->
        <span class="spBlock mb5">参数：<i>status: 0为待付款、1为待发货、2为待收货、3为待评价、4为已完成、无参数为全部订单；</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/order_list/index?status=0</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">物流信息</span>
        <span class="spBlock mb5">地址：<i>/pages/users/goods_logistics/index</i></span>
        <span class="spBlock mb5">参数：<i>orderId：订单号</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/goods_logistics/index?orderId=wx158553586478526437</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">退货列表</span>
        <span class="spBlock">地址：<i>/pages/users/refund/list</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">退货详情</span>
        <span class="spBlock mb5">地址：<i>/pages/users/refund/detail</i></span>
        <span class="spBlock mb5">参数：<i>id：退款列表id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/refund/detail?id=7</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">退货物流信息</span>
        <span class="spBlock mb5">地址：<i>/pages/users/refund/logistics</i></span>
        <span class="spBlock mb5">参数：<i>orderId：退款订单id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/refund/logistics?orderId=7</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">申请退货</span>
        <span class="spBlock mb5">地址：<i>/pages/users/goods_return/index</i></span>
        <span class="spBlock mb5">参数：<i>orderId：订单号</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/goods_return/index?orderId=110</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">登录</span>
        <span class="spBlock">地址：<i>/pages/users/login/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">商品评分</span>
        <span class="spBlock mb5">地址：<i>pages/users/goods_comment_list/index</i></span>
        <span class="spBlock mb5">参数：<i>product_id：商品id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/goods_comment_list/index?product_id=78</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">商品评价</span>
        <span class="spBlock mb5">地址：<i>/pages/users/goods_comment_con/index</i></span>
        <span class="spBlock mb5">参数：<i>unique：产品属性的唯一值；uni：订单号</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/goods_comment_con/index?unique=c0590f62af7e06b7b53806bc75713a25&uni=wx158553456394392924</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">查看物流</span>
        <span class="spBlock mb5">地址：<i>/pages/users/goods_logistics/indexx</i></span>
        <span class="spBlock mb5">参数：<i>orderId：订单id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/users/goods_logistics/index?orderId=57</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单首页</span>
        <span class="spBlock">地址：<i>/pages/admin/order/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单列表</span>
        <span class="spBlock mb5">地址：<i>/pages/admin/orderList/index</i></span>
        <span class="spBlock mb5">参数：<i>types：1为待付款、2为待发货、3为待收货、4为待评价、6为退款</i></span>
        <span class="spBlock spEx">例如：<i>/pages/admin/orderList/index?types=1</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单详情</span>
        <span class="spBlock mb5">地址：<i>/pages/admin/orderDetail/index</i></span>
        <span class="spBlock mb5">参数：<i>order_id：订单编号</i></span>
        <span class="spBlock spEx">例如：<i>/pages/admin/orderDetail/index?order_id=wx158564762986963277</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单发货</span>
        <span class="spBlock mb5">地址：<i>/pages/admin/delivery/index</i></span>
        <span class="spBlock mb5">参数：<i>order_id：订单编号</i></span>
        <span class="spBlock spEx">例如：<i>/pages/admin/delivery/index?order_id=wx158564762986963277</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单数据统计</span>
        <span class="spBlock mb5">地址：<i>/pages/admin/statistics/index</i></span>
        <span class="spBlock mb5">参数：<i>type：1：成交额统计，2：订单数统计；time：today：今天、yesterday：昨天、month：本月、seven：最近7天；</i></span>
        <span class="spBlock spEx">例如：<i>/pages/admin/statistics/index?type=1&time=today</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单核销</span>
        <span class="spBlock">地址：<i>/pages/admin/order_cancellation/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">热门榜单/ 精品推荐/ 首发新品/促销单品</span>
        <span class="spBlock mb5">地址：<i>/pages/columnGoods/HotNewGoods/index</i></span>
        <span class="spBlock mb5">参数：<i>type: 区分列表：best为精品推荐、hot为热门榜单、new为首发新品、good为促销单品</i></span>
        <span class="spBlock spEx">例如：<i>/pages/columnGoods/HotNewGoods/index?type=best</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">店铺首页</span>
        <span class="spBlock mb5">地址：<i>/pages/store/home/index</i></span>
        <span class="spBlock mb5">参数：<i>id：店铺id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/store/home/index?id=56</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">店铺详情</span>
        <span class="spBlock mb5">地址：<i>/pages/store/detail/index</i></span>
        <span class="spBlock mb5">参数：<i>id：店铺id</i></span>
        <span class="spBlock spEx">例如：<i>/pages/store/detail/index?id=56</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">商家入驻</span>
        <span class="spBlock mb5">地址：<i>/pages/store/settled/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单管理</span>
        <span class="spBlock mb5">地址：<i>/pages/admin/order/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">订单数据统计</span>
        <span class="spBlock mb5">地址：<i>/pages/admin/statistics/index</i></span>
        <span class="spBlock mb5">参数：<i>type: price为成交金额、order为订单数，time：today：今天、yesterday：昨天、month：本月</i></span>
        <span class="spBlock spEx">例如：<i>/pages/admin/statistics/index?type=price&time=today</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">秒杀活动列表</span>
        <span class="spBlock mb5">地址：<i>/pages/activity/goods_seckill/index</i></span>
        <el-divider></el-divider>
      </div>
      <div>
        <span class="spBlock spTitle mb10">直播列表</span>
        <span class="spBlock mb5">地址：<i>/pages/activity/liveBroadcast/index</i></span>
        <el-divider></el-divider>
      </div>
    </el-card>
  </div>
</template>

<script>
export default {
  name: 'PageLinks'
}
</script>

<style scoped lang="scss">
  .goodsTitle {
    .title {
      border-left: 2px solid #1890FF;
      padding-left: 10px;
      color: #000;
      font-size: 14px;
      box-sizing: border-box;
    }
  }
  .spTitle{
    font-size: 14px;
    color: #1890FF;
  }
  .spEx{
    i{
      font-size: 14px;
      color: #666 !important;
    }
  }
  .spBlock{
    i{
      font-style: normal;
      color: #666;
    }
  }
  .spEx{
    i{
      font-style: normal;
    }
  }
</style>
