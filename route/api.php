<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

use app\common\middleware\AllowOriginMiddleware;
use app\common\middleware\CheckSiteOpenMiddleware;
use app\common\middleware\InstallMiddleware;
use app\common\middleware\UserTokenMiddleware;
use app\common\middleware\VisitProductMiddleware;
use think\facade\Route;

Route::group('api/', function () {
    Route::get('test', 'api.Auth/test');
    //强制登录
    Route::group(function () {
        //退出登录
        Route::post('logout', 'api.Auth/logout');
        //用户信息
        Route::get('user', 'api.Auth/userInfo');

        //优惠券
        Route::group('coupon', function () {
            Route::post('receive/:id', 'api.store.product.StoreCoupon/receiveCoupon');
            Route::get('list', 'api.store.product.StoreCoupon/lst');
        });

        //客服聊天
        Route::group('service', function () {
            Route::get('history/:id', 'api.store.service.Service/chatHistory');
            Route::get('list', 'api.store.service.Service/getList');
            Route::get('mer_history/:merId/:id', 'api.store.service.Service/serviceHistory');
            Route::get('user_list', 'api.store.service.Service/serviceUserList');
        });

        //订单
        Route::group('order', function () {
            Route::post('check', '/checkOrder');
            Route::post('create', '/createOrder');
            Route::get('group_order_list', '/groupOrderList');
            Route::get('group_order_detail/:id', '/groupOrderDetail');
            Route::post('cancel/:id', '/cancelGroupOrder');
            Route::get('list', '/lst');
            Route::get('detail/:id', '/detail');
            Route::get('number', '/number');
            Route::post('pay/:id', '/groupOrderPay');
            Route::post('take/:id', '/take');
            Route::post('express/:id', '/express');
            Route::post('del/:id', '/del');
            Route::get('status/:id', '/groupOrderStatus');
            Route::get('verify_code/:id', '/verifyCode');
        })->prefix('api.store.order.StoreOrder');

        //退款单
        Route::group('refund', function () {
            Route::get('batch_product/:id', '/batchProduct');
            Route::get('express/:id', '/express');
            Route::get('product/:id', '/product');
            Route::post('apply/:id', '/refund');
            Route::get('list', '/lst');
            Route::get('detail/:id', '/detail');
            Route::post('del/:id', '/del');
            Route::post('back_goods/:id', '/back_goods');
        })->prefix('api.store.order.StoreRefundOrder');

        //评价
        Route::group('reply', function () {
            Route::get('product/:id', '/product');
            Route::post(':id', '/reply');
        })->prefix('api.store.product.StoreReply');

        //核销
        Route::group('verifier', function () {
            Route::get('order/:id', '/detail');
            Route::get('lst', '/lst');
            Route::post(':id', '/verify');
        })->prefix('api.store.order.StoreOrderVerify');

        //用户
        Route::group('user', function () {
            //切换账号
            Route::get('account', 'User/account');
            Route::post('switch', 'User/switchUser');

            //收藏
            Route::get('/relation/product/lst', 'UserRelation/productList');
            Route::get('/relation/merchant/lst', 'UserRelation/merchantList');
            Route::post('/relation/create', 'UserRelation/create');
            Route::post('/relation/batch/create', 'UserRelation/batchCreate');
            Route::post('/relation/delete', 'UserRelation/delete');

            //反馈
            Route::post('/feedback', 'Feedback/feedback');
            Route::get('/feedback/list', 'Feedback/feedbackList');
            Route::get('/feedback/detail/:id', 'Feedback/detail');
            //充值
            Route::post('/recharge', 'UserRecharge/recharge');
            Route::post('/recharge/brokerage', 'UserRecharge/brokerage');
            //地址
            Route::get('/address/lst', 'UserAddress/lst');
            Route::post('/address/create', 'UserAddress/create');
            Route::get('/address/detail/:id', 'UserAddress/detail');
            Route::post('/address/update/:id', 'UserAddress/editDefault');
            Route::post('/address/delete/:id', 'UserAddress/delete');

            //分销海报
            Route::get('/spread_image', 'User/spread_image');
            //推广人列表
            Route::get('/spread_list', 'User/spread_list');

            //提现
            Route::get('/extract/lst', 'UserExtract/lst');
            Route::get('/extract/banklst', 'UserExtract/bankLst');
            Route::post('/extract/create', 'UserExtract/create');

            //绑定手机号
            Route::post('binding', 'User/binding');

            //余额记录
            Route::get('bill', 'User/bill');
            //佣金记录
            Route::get('brokerage_list', 'User/brokerage_list');
            //推广人订单
            Route::get('spread_order', 'User/spread_order');
            //推广人排行榜
            Route::get('spread_top', 'User/spread_top');
            //佣金排行榜
            Route::get('brokerage_top', 'User/brokerage_top');
            Route::get('spread_info', 'User/spread_info');

            //浏览记录
            Route::get('history', 'User/history');
            Route::post('history/delete/:id', 'User/deleteHistory');
        })->prefix('api.user.');

        //购物车
        Route::group('user/cart', function () {
            Route::get('/lst', 'StoreCart/lst');
            Route::post('/create', 'StoreCart/create');
            Route::post('/again', 'StoreCart/again');
            Route::post('/change/:id', 'StoreCart/change');
            Route::post('/delete', 'StoreCart/batchDelete');
            Route::get('/count', 'StoreCart/cartCount');
        })->prefix('api.store.order.');

    })->middleware(UserTokenMiddleware::class, true);

    //非强制登录
    Route::group(function () {
        //上传图片
        Route::post('upload/image/:field', 'api.Common/uploadImage');
        //公共配置
        Route::get('config', 'api.Common/config');

        //商品
        Route::group('store/product', function () {
            Route::get('seckill/select', 'StoreSeckillProduct/select');
            Route::get('seckill/lst', 'StoreSeckillProduct/lst');
            Route::get('seckill/detail/:id', 'StoreSeckillProduct/detail');
            Route::get('recommend/lst', 'StoreProduct/recommendList');
            Route::get('lst', 'StoreProduct/lst');
            Route::get('hot/:type', 'StoreProduct/hot');
            Route::get('category/lst', 'StoreCategory/lst');
            Route::get('category', 'StoreCategory/children');
            Route::get('brand/lst', 'StoreBrand/lst');
            Route::get('detail/:id', 'StoreProduct/detail')->middleware(VisitProductMiddleware::class);
            Route::get('/qrcode/:id', 'StoreProduct/qrcode');
            Route::get('bag', 'StoreProduct/getBagList');
            Route::get('bag/recommend', 'StoreProduct/getBagRecomm');
            Route::get('bag/explain', 'StoreProduct/getBagExplain');
            Route::get('/reply/lst/:id', 'StoreReply/lst');
        })->prefix('api.store.product.');

        //直播
        Route::group('broadcast', function () {
            Route::get('/lst', 'BroadcastRoom/lst');
            Route::get('/hot', 'BroadcastRoom/hot');

        })->prefix('api.store.broadcast.');

        //优惠券
        Route::group('coupon', function () {
            Route::get('product', 'api.store.product.StoreCoupon/coupon');
            Route::get('store/:id', 'api.store.product.StoreCoupon/merCoupon');
        });

        //商户
        Route::group('store/merchant/', function () {
            Route::get('/lst', 'Merchant/lst');
            Route::get('/product/lst/:id', 'Merchant/productList');
            Route::get('/category/lst/:id', 'Merchant/categoryList');
            Route::get('/detail/:id', 'Merchant/detail');
            Route::get('/qrcode/:id', 'Merchant/qrcode');
        })->prefix('api.store.merchant.');

        //文章
        Route::group('article', function () {
            Route::get('/lst/:cid', 'Article/lst');
            Route::get('detail/:id', 'Article/detail');
            Route::get('/category/lst', 'ArticleCategory/lst');
        })->prefix('api.article.');

        //管理员
        Route::group('admin', function () {
            Route::get('/statistics', '/orderStatistics');
            Route::get('/order_price', '/orderDetail');
            Route::get('/order_list', '/orderList');
            Route::get('/order/:id', '/order');
            Route::post('/mark/:id', '/mark');
            Route::post('/price/:id', '/price');
            Route::post('/delivery/:id', '/delivery');
            Route::get('/pay_price', '/payPrice');
            Route::get('/pay_number', '/payNumber');
        })->prefix('api.user.Admin');
        Route::get('excel/download/:id', 'merchant.store.order.Order/download');
        //申请商户
        Route::post('intention/create', 'api.store.merchant.MerchantIntention/create');
        Route::get('intention/cate', 'api.store.merchant.MerchantIntention/cateLst');
        //浏览量
        Route::post('common/visit', 'api.Common/visit');

    })->middleware(UserTokenMiddleware::class, false);

    //微信支付回调
    Route::any('notice/wechat_pay', 'api.Common/wechatNotify')->name('wechatNotify');
    //小程序支付回调
    Route::any('notice/routine_pay', 'api.Common/routineNotify')->name('routineNotify');

    //城市列表
    Route::get('system/city/lst', 'merchant.store.shipping.City/getlist');
    //热门搜索
    Route::get('common/hot_keyword', 'api.Common/hotKeyword');
    //推荐页 banner
    Route::get('common/hot_banner/:type', 'api.Common/hotBanner');
    //退款原因
    Route::get('common/refund_message', 'api.Common/refundMessage');
    //充值赠送
    Route::get('common/recharge_quota', 'api.Common/userRechargeQuota');
    //快递公司
    Route::get('common/express', 'api.Common/express');
    //图片转 base64
    Route::post('common/base64', 'api.Common/get_image_base64');
    //个人中心菜单
    Route::get('common/menus', 'api.Common/menus');
    //首页数据
    Route::get('common/home', 'api.Common/home');
    //用户反馈类型
    Route::get('common/feedback_type', 'api.user.FeedBackCategory/lst');
    //登录
    Route::post('auth/login', 'api.Auth/login');
    //短信登录
    Route::post('auth/smslogin', 'api.Auth/smsLogin');
    //微信授权
    Route::get('auth/wechat', 'api.Auth/auth');
    //小程序授权
    Route::post('auth/mp', 'api.Auth/mpAuth');
    //验证码
    Route::post('auth/verify', 'api.Auth/verify');
    //微信配置
    Route::get('wechat/config', 'api.Wechat/jsConfig');
    //图片验证码
    Route::get('captcha', 'api.Auth/getCaptcha');
})->middleware(AllowOriginMiddleware::class)->middleware(InstallMiddleware::class)
    ->middleware(CheckSiteOpenMiddleware::class);

Route::any('/', 'View/h5')->middleware(InstallMiddleware::class)
    ->middleware(CheckSiteOpenMiddleware::class);

Route::group('/pages', function () {
    Route::miss('View/h5');
})->middleware(InstallMiddleware::class)
    ->middleware(CheckSiteOpenMiddleware::class);
