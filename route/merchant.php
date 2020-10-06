<?php

use think\facade\Route;


Route::group(config('admin.merchant_prefix'), function () {
    Route::miss(function () {
        $DB = DIRECTORY_SEPARATOR;
        return view(app()->getRootPath() . 'public' . $DB . 'mer.html');
    });
})->middleware(\app\common\middleware\InstallMiddleware::class)
    ->middleware(\app\common\middleware\CheckSiteOpenMiddleware::class);

Route::group(config('admin.api_merchant_prefix') . '/', function () {
    Route::group(function () {

        Route::get('menus', 'admin.system.auth.Menu/menus')->append(['merchant' => 1]);
        Route::get('logout', 'merchant.system.admin.Login/logout');
        Route::get('info', 'merchant.system.Merchant/info');
        Route::get('user/list', 'merchant.user.User/getUserList');
        Route::get('update/form', 'merchant.system.Merchant/updateForm')->name('merchantUpdateForm');
        Route::post('update', 'merchant.system.Merchant/update')->name('merchantUpdate');
        //导出文件
        Route::get('excel/lst', 'merchant.store.Excel/lst')->name('merchantStoreExcelLst');
        Route::get('excel/download/:id', 'merchant.store.Excel/download')->name('merchantStoreExcelDownload');
        //配置
        Route::get('config/:key', 'admin.system.config.Config/form')->name('merchantConfigForm');
        Route::post('config/save/:key', 'admin.system.config.ConfigValue/save')->name('merchantConfigSave');

        Route::group('take', function () {
            Route::get('info', 'merchant.system.Merchant/takeInfo')->name('merchantTakeInfo');
            Route::post('update', 'merchant.system.Merchant/take')->name('merchantTakeUpdate');
        });

        Route::group('service', function () {
            Route::get('list', 'StoreService/lst')->name('merchantServiceLst');
            Route::post('create', 'StoreService/create')->name('merchantServiceCreate');
            Route::get('create/form', 'StoreService/createForm')->name('merchantServiceCreateForm');
            Route::post('update/:id', 'StoreService/update')->name('merchantServiceUpdate');
            Route::get('update/form/:id', 'StoreService/updateForm')->name('merchantServiceUpdateForm');
            Route::post('status/:id', 'StoreService/changeStatus')->name('merchantServiceSwitchStatus');
            Route::delete('delete/:id', 'StoreService/delete')->name('merchantServiceDelete');
            Route::get('/:id/user', 'StoreService/serviceUserList')->name('merchantServiceServiceUserList');
            Route::get('/:id/:uid/lst', 'StoreService/getUserMsnByService')->name('merchantServiceServiceUserLogLst');
            Route::get('mer/:id/user', 'StoreService/merchantUserList')->name('merchantServiceServiceMerchantUserList');
            Route::get('/:id/lst', 'StoreService/getUserMsnByMerchant')->name('merchantServiceMerchantUserLogLst');
        })->prefix('merchant.store.service.');

        //身份规则
        Route::group('system/role', function () {
            Route::get('lst', '/getList')->name('merchantRoleGetList');
            Route::post('create', '/create')->name('merchantRoleCreate');
            Route::get('create/form', '/createForm')->name('merchantRoleCreateForm');
            Route::post('update/:id', '/update')->name('merchantRoleUpdate');
            Route::get('update/form/:id', '/updateForm')->name('merchantRoleUpdateForm');
            Route::post('status/:id', '/switchStatus')->name('merchantRoleStatus');
            Route::delete('delete/:id', '/delete')->name('merchantRoleDelete');
        })->prefix('merchant.system.auth.Role');


        //Admin管理
        Route::group('system/admin', function () {
            Route::get('lst', '/getList')->name('merchantAdminLst');
            Route::post('status/:id', '/switchStatus')->name('merchantAdminStatus');
            Route::post('create', '/create')->name('merchantAdminCreate');
            Route::get('create/form', '/createForm')->name('merchantAdminCreateForm');
            Route::post('update/:id', '/update')->name('merchantAdminUpdate');
            Route::get('update/form/:id', '/updateForm')->name('merchantAdminUpdateForm');
            Route::post('password/:id', '/password')->name('merchantAdminPassword');
            Route::get('password/form/:id', '/passwordForm')->name('merchantAdminPasswordForm');
            Route::delete('delete/:id', '/delete')->name('merchantAdminDelete');
            Route::get('edit/form', '/editForm')->name('merchantAdminEditForm');
            Route::post('edit', '/edit')->name('merchantAdminEdit');
            Route::get('edit/password/form', '/editPasswordForm')->name('merchantAdminEditPasswordForm');
            Route::post('edit/password', '/editPassword')->name('merchantAdminEditPassword');
        })->prefix('merchant.system.admin.MerchantAdmin');


        //附件管理
        Route::group('system/attachment', function () {
            Route::get('lst', '/getList')->name('merchantAttachmentLst');
            Route::delete('delete', '/delete')->name('merchantAttachmentDelete');
            Route::post('category', '/batchChangeCategory')->name('merchantAttachmentBatchChangeCategory');
        })->prefix('admin.system.attachment.Attachment');

        //上传图片
        Route::post('upload/image/:id/:field', 'admin.system.attachment.Attachment/image')->name('merchantUploadImage');
        Route::get('system/admin/log', 'admin.system.admin.AdminLog/lst')->name('merchantAdminLog');

        //附件分类管理
        Route::group('system/attachment/category', function () {
            Route::get('formatLst', '/getFormatList')->name('merchantAttachmentCategoryGetFormatList');
            Route::get('create/form', '/createForm')->name('merchantAttachmentCategoryCreateForm');
            Route::get('update/form/:id', '/updateForm')->name('merchantAttachmentCategoryUpdateForm');
            Route::post('create', '/create')->name('merchantAttachmentCategoryCreate');
            Route::post('update/:id', '/update')->name('merchantAttachmentCategoryUpdate');
            Route::delete('delete/:id', '/delete')->name('merchantAttachmentCategoryDelete');
        })->prefix('admin.system.attachment.AttachmentCategory');


        //产品规则模板
        Route::group('store/attr/template', function () {
            Route::get('lst', '/lst')->name('merchantStoreAttrTemplateLst');
            Route::get('list', '/getlist');
            Route::post('create', '/create')->name('merchantStoreAttrTemplateCreate');
            Route::delete(':id', '/delete')->name('merchantStoreAttrTemplateDelete');
            Route::post(':id', '/update')->name('merchantStoreAttrTemplateUpdate');
        })->prefix('merchant.store.StoreAttrTemplate');

        //商品分类
        Route::group('store/category', function () {
            Route::get('create/form', '/createForm')->name('merchantStoreCategoryCreateForm');
            Route::get('update/form/:id', '/updateForm')->name('merchantStoreCategoryUpdateForm');
            Route::post('update/:id', '/update')->name('merchantStoreCategoryUpdate');
            Route::get('lst', '/lst')->name('merchantStoreCategoryLst');
            Route::get('detail/:id', '/detail')->name('merchantStoreCategoryDtailt');
            Route::post('create', '/create')->name('merchantStoreCategoryCreate');
            Route::delete('delete/:id', '/delete')->name('merchantStoreCategoryDelete');
            Route::post('status/:id', '/switchStatus')->name('merchantStoreCategorySwitchStatus');
            Route::get('list', '/getList');
            Route::get('select', '/getTreeList');
            Route::get('brandlist', '/BrandList');

        })->prefix('admin.store.StoreCategory');

        //优惠券
        Route::group('store/coupon', function () {
            Route::get('create/form', '/createForm')->name('merchantCouponCreateForm');
            Route::get('clone/form/:id', '/cloneForm')->name('merchantCouponIssueCloneForm');
            Route::post('create', '/create')->name('merchantCouponCreate');
            Route::post('status/:id', '/changeStatus')->name('merchantCouponIssueChangeStatus');
            Route::get('lst', '/lst')->name('merchantCouponLst');
            Route::get('issue', '/issue')->name('merchantCouponIssue');
            Route::get('select', '/select');
            Route::post('/:id/user', '/sendCoupon')->name('merchantCouponSendCoupon');
            Route::delete('delete/:id', '/delete')->name('merchantCouponDelete');
            Route::get('detail/:id', '/detail')->name('merchantCouponDetail');
        })->prefix('merchant.store.coupon.Coupon');

        //地址信息
        Route::get('system/city/lst', 'merchant.store.shipping.City/lst');
        //运费模板
        Route::group('store/shipping', function () {
            Route::get('lst', '/lst')->name('merchantStoreShippingTemplateLst');
            Route::get('list', '/getList');
            Route::post('create', '/create')->name('merchantStoreShippingTemplateCreate');
            Route::post('update/:id', '/update')->name('merchantStoreShippingTemplateUpdate');
            Route::get('detail/:id', '/detail')->name('merchantStoreShippingTemplateDetail');
            Route::delete('delete/:id', '/delete')->name('merchantStoreShippingTemplateDelete');
        })->prefix('merchant.store.shipping.ShippingTemplate');

        /*  未使用
         Route::group('store/shipping',function(){
               Route::delete('region/delete/:id','merchant.store.ShippingTemplateRegion/delete')->name('storeShippingTemplateRegionDelete');
               Route::delete('free/delete/:id','merchant.store.ShippingTemplateFree/delete')->name('storeShippingTemplateFreeDelete');
               Route::delete('undelive/delete/:id','merchant.store.ShippingTemplateUndelive/delete')->name('storeShippingTemplateUndeliveDelete');
           });
        */
        //商品
        Route::group('store/product', function () {
            Route::get('config', '/config');
            Route::get('lst_filter', '/getStatusFilter')->name('merchantStoreProductLstFilter');
            Route::get('lst', '/lst')->name('merchantStoreProductLst');
            Route::get('list', '/lst');
            Route::post('create', '/create')->name('merchantStoreProductCreate');
            Route::get('detail/:id', '/detail')->name('merchantStoreProductDetail');
            Route::post('update/:id', '/update')->name('merchantStoreProductUpdate');
            Route::delete('delete/:id', '/delete')->name('merchantStoreProductDelete');
            Route::delete('destory/:id', '/destory')->name('merchantStoreProductDestory');
            Route::post('restore/:id', '/restore')->name('merchantStoreProductRestore');
            Route::post('status/:id', '/switchStatus')->name('merchantStoreProductSwitchStatus');
        })->prefix('merchant.store.product.Product');

        //秒杀
        Route::group('store/seckill_product', function () {
            Route::get('lst_time', '/lst_time');
            Route::get('lst_filter', '/getStatusFilter')->name('merchantStoreSeckillProductLstFilter');
            Route::get('lst', '/lst')->name('merchantStoreSeckillProductLst');
            Route::post('create', '/create')->name('merchantStoreSeckillProductCreate');
            Route::get('detail/:id', '/detail')->name('merchantStoreSeckillProductDetail');
            Route::post('update/:id', '/update')->name('merchantStoreSeckillProductUpdate');
            Route::delete('delete/:id', '/delete')->name('merchantStoreSeckillProductDelete');
            Route::delete('destory/:id', '/destory')->name('merchantStoreSeckillProductDestory');
            Route::post('restore/:id', '/restore')->name('merchantStoreSeckillProductRestore');
            Route::post('status/:id', '/switchStatus')->name('merchantStoreSeckillProductSwitchStatus');
        })->prefix('merchant.store.product.SeckillProduct');

        Route::group('store/productcopy', function () {
            Route::get('lst', '/lst')->name('merchantStoreProductCopyLst');
            Route::get('get', '/get')->name('merchantStoreProductCopyGet');
            Route::get('count', '/count')->name('merchantStoreProductCopyCount');
        })->prefix('merchant.store.product.ProductCopy');

        //直播间
        Route::group('broadcast/room', function () {
            Route::get('lst', '/lst')->name('merchantBroadcastRoomLst');
            Route::get('detail/:id', '/detail')->name('merchantBroadcastRoomDetail');
            Route::get('create/form', '/createForm')->name('merchantBroadcastRoomCreateForm');
            Route::post('create', '/create')->name('merchantBroadcastRoomCreate');
            Route::post('status/:id', '/changeStatus')->name('merchantBroadcastRoomChangeStatus');
            Route::post('export_goods', '/exportGoods')->name('merchantBroadcastRoomExportGoods');
            Route::post('mark/:id', '/mark')->name('merchantBroadcastRoomMark');
            Route::get('goods/:id', '/goodsList')->name('merchantBroadcastRoomGoods');
            Route::delete('delete/:id', '/delete')->name('merchantBroadcastRoomDelete');
        })->prefix('merchant.store.broadcast.BroadcastRoom');

        //直播间商品
        Route::group('broadcast/goods', function () {
            Route::get('lst', '/lst')->name('merchantBroadcastGoodsLst');
            Route::get('detail/:id', '/detail')->name('merchantBroadcastGoodsDetail');
            Route::get('create/form', '/createForm')->name('merchantBroadcastGoodsCreateForm');
            Route::post('create', '/create')->name('merchantBroadcastGoodsCreate');
            Route::get('update/form/:id', '/updateForm')->name('merchantBroadcastGoodsUpdateForm');
            Route::post('update/:id', '/update')->name('merchantBroadcastGoodsUpdate');
            Route::post('status/:id', '/changeStatus')->name('merchantBroadcastGoodsChangeStatus');
            Route::post('mark/:id', '/mark')->name('merchantBroadcastGoodsMark');
            Route::delete('delete/:id', '/delete')->name('merchantBroadcastGoodsDelete');
            Route::post('batch_create', '/batchCreate')->name('merchantBroadcastGoodsbatchCreate');
        })->prefix('merchant.store.broadcast.BroadcastGoods');


        //Order
        Route::group('store/order', function () {
            Route::get('excel', 'Order/excel')->name('merchantStoreOrderExcel');
            Route::get('printer/:id', 'Order/printer')->name('merchantStoreOrderPrinter');
            Route::get('chart', 'Order/chart')->name('merchantStoreOrderTitle');
            Route::get('takechart', 'Order/takeChart')->name('merchantStoreTakeOrderTitle');
            Route::get('filtter', 'Order/orderType')->name('merchantStoreOrderFiltter');
            Route::get('lst', 'Order/lst')->name('merchantStoreOrderLst');
            Route::get('takelst', 'Order/takeLst')->name('merchantStoreTakeOrderLst');
            Route::get('express/:id', 'Order/express')->name('merchantStoreOrderExpress');
            Route::get('delivery/:id/form', 'Order/deliveryForm')->name('merchantStoreOrderDeliveryForm');
            Route::get('update/:id/form', 'Order/updateForm')->name('merchantStoreOrderUpdateForm');
            Route::post('update/:id', 'Order/update')->name('merchantStoreOrderUpdate');
            Route::post('delivery/:id', 'Order/delivery')->name('merchantStoreOrderDelivery');
            Route::get('detail/:id', 'Order/detail')->name('merchantStoreOrderDetail');
            Route::get('log/:id', 'Order/status')->name('merchantStoreOrderLog');
            Route::get('remark/:id/form', 'Order/remarkForm')->name('merchantStoreOrderRemarkForm');
            Route::post('remark/:id', 'Order/remark')->name('merchantStoreOrderRemark');
            Route::post('verify/:code', 'Order/verify')->name('merchantStoreOrderVerify');
            Route::post('delete/:id', 'Order/delete')->name('merchantStoreOrderDelete');
            Route::get('reconciliation/lst', 'Reconciliation/lst')->name('sysMerchantReconciliationLst');
            Route::get('reconciliation/mark/:id/form', 'Reconciliation/markForm')->name('merchantReconciliationMarkForm');
            Route::post('reconciliation/mark/:id', 'Reconciliation/mark')->name('merchantReconciliationMark');
            Route::post('reconciliation/status/:id', 'Reconciliation/switchStatus')->name('merchantReconciliationSwitchStatus');
            Route::get('reconciliation/:id/order', 'Order/reList')->name('merchantReconciliationOrderReList');
            Route::get('reconciliation/:id/refund', 'RefundOrder/reList')->name('merchantReconciliationRefundReList');
        })->prefix('merchant.store.order.');

        //退款订单
        Route::group('store/refundorder', function () {
            Route::get('lst', '/lst')->name('merchantStoreRefundOrderLst');
            Route::get('detail/:id', '/detail')->name('merchantStoreRefundOrderDetail');
            Route::get('status/:id/form', '/switchStatusForm')->name('merchantStoreRefundOrderSwitchStatusForm');
            Route::post('status/:id', '/switchStatus')->name('merchantStoreRefundOrderSwitchStatus');
            Route::post('refund/:id', '/refundPrice')->name('merchantStoreRefundOrderRefund');
            Route::delete('delete/:id', '/delete')->name('merchantStoreRefundDelete');
            Route::get('mark/:id/form', '/markForm')->name('merchantStoreRefundMarkForm');
            Route::post('mark/:id', '/mark')->name('merchantStoreRefundMark');
            Route::get('log/:id', '/log')->name('merchantStoreRefundLog');
            Route::get('express/:id', '/express')->name('merchantStoreRefundExpress');
        })->prefix('merchant.store.order.RefundOrder');

        Route::group('statistics', function () {
            Route::get('main', '/main')->name('merchantStatisticsMain');
            Route::get('order', '/order')->name('merchantStatisticsOrder');
            Route::get('user', '/user')->name('merchantStatisticsUser');
            Route::get('user_rate', '/userRate')->name('merchantStatisticsUserRate');
            Route::get('product', '/product')->name('merchantStatisticsProduct');
            Route::get('product_visit', '/productVisit')->name('merchantStatisticsProductVisit');
            Route::get('product_cart', '/productCart')->name('merchantStatisticsProductCart');
        })->prefix('merchant.Common');

        //资金明细
        Route::group('financial_record', function () {
            Route::get('list', '/lst')->name('merchantFinancialRecordList');
            Route::get('export', '/export')->name('merchantFinancialRecordExport');
        })->prefix('admin.system.merchant.FinancialRecord');


        //商品评价管理
        Route::group('store/reply', function () {
            Route::get('lst', '/lst')->name('merchantProductReplyLst');
            Route::get('form/:id', '/replyForm')->name('merchantProductReplyForm');
            Route::post('reply/:id', '/reply')->name('merchantProductReplyReply');
        })->prefix('admin.store.StoreProductReply');

        Route::get('envet', 'merchant.user.User/envt');
    })->middleware(\app\common\middleware\AllowOriginMiddleware::class)
        ->middleware(\app\common\middleware\MerchantTokenMiddleware::class, true)
        ->middleware(\app\common\middleware\MerchantAuthMiddleware::class)
        ->middleware(\app\common\middleware\LogMiddleware::class);

//不带token认证
    Route::group(function () {
        Route::get('test', 'merchant.system.admin.Login/test');

        //验证码
        Route::get('captcha', 'merchant.system.admin.Login/getCaptcha');
        //登录
        Route::post('login', 'merchant.system.admin.Login/login');

        Route::get('login_config', 'admin.Common/loginConfig');

        Route::group(function () {

        })->middleware(\app\common\middleware\MerchantTokenMiddleware::class, false);

    })->middleware(\app\common\middleware\AllowOriginMiddleware::class);

    Route::miss(function () {
        return app('json')->fail('接口不存在');
    })->middleware(\app\common\middleware\AllowOriginMiddleware::class);
})->middleware(\app\common\middleware\InstallMiddleware::class)
    ->middleware(\app\common\middleware\CheckSiteOpenMiddleware::class);
