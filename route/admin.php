<?php

use app\common\middleware\AdminAuthMiddleware;
use app\common\middleware\AdminTokenMiddleware;
use app\common\middleware\AllowOriginMiddleware;
use app\common\middleware\InstallMiddleware;
use app\common\middleware\LogMiddleware;
use think\facade\Route;

Route::group(config('admin.admin_prefix'), function () {
    Route::miss(function () {
        $DB = DIRECTORY_SEPARATOR;
        return view(app()->getRootPath() . 'public' . $DB . 'system.html');
    });
})->middleware(InstallMiddleware::class);

Route::group(config('admin.api_admin_prefix') . '/', function () {

    Route::group(function () {
        //短信
        Route::group('sms', function () {
            //保存配置 登录
            Route::post('config', '.Sms/save_basics')->name('smsLogin');
            //短信发送记录
            Route::get('record', '.Sms/record')->name('smsRecord');
            //短信账号数据
            Route::get('data', '.Sms/data')->name('smsData');
            //查看是否登录
            Route::get('is_login', '.Sms/is_login');
            //退出登录
            Route::get('logout', '.Sms/logout')->name('smsLogout');
            //发送短信验证码
            Route::post('captcha', '.Sms/captcha')->name('smsCaptcha');
            //修改/注册短信平台账号
            Route::post('register', '.Sms/save')->name('smsSave');
            //短信模板列表
            Route::get('temp', '.SmsTemplate/template')->name('smsTemplate');
            //短信模板申请表单
            Route::get('temp/form', '.SmsTemplate/form')->name('smsCreateForm');
            //短信模板申请
            Route::post('temp', '.SmsTemplate/apply')->name('smsCreate');
            //公共短信模板列表
            Route::get('public', '.SmsTemplate/public')->name('smsPublicTemplate');
            //剩余条数
            Route::get('number', '.SmsPay/number')->name('smsNumber');
            //获取支付套餐
            Route::get('price', '.SmsPay/price')->name('smsPrice');
            //获取支付码
            Route::post('pay_code', '.SmsPay/pay')->name('smsPay');
            //修改密码
            Route::post('change_password', '.Sms/changePassword')->name('smsChangePassword');
            //修改簽名
            Route::post('change_sign', '.Sms/changeSign')->name('smsChangeSign');
        })->prefix('admin.system.sms');


        //配置分类
        Route::group('config/classify', function () {
            Route::get('create/table', '/createTable')->name('configClassifyCreateForm');
            Route::post('create', '/create')->name('configClassifyCreate');
            Route::delete('delete/:id', '/delete')->name('configClassifyDelete');
            Route::post('update/:id', '/update')->name('configClassifyUpdate');
            Route::get('update/table/:id', '/updateTable')->name('configClassifyUpdateForm');
            Route::post('status/:id', '/switchStatus')->name('configClassifySwitchStatus');
            Route::get('lst', '/lst')->name('configClassifyLst');
        })->prefix('admin.system.config.ConfigClassify');

        Route::get('menus', 'admin.system.auth.Menu/menus');
        //配置
        Route::group('config/setting', function () {
            Route::get('create/table', '/createTable')->name('configSettingCreateForm');
            Route::post('create', '/create')->name('configSettingCreate');
            Route::post('update/:id', '/update')->name('configSettingUpdate');
            Route::get('update/table/:id', '/updateTable')->name('configSettingUpdateForm');
            Route::post('status/:id', '/switchStatus')->name('configSettingSwitchStatus');
            Route::get('lst', '/lst')->name('configSettingLst');
            Route::delete('delete/:id', '/delete')->name('configSettingDelete');
            Route::post('upload_file/:field', '/upload')->name('configUpload');
        })->prefix('admin.system.config.Config');

        Route::group('config/others', function () {
            Route::get('lst', 'ConfigOthers/lst')->name('configOthersSettingLst');
            Route::post('update', 'ConfigOthers/update')->name('configOthersSettingUpdate');
        })->prefix('admin.system.config.');

        Route::get('config/:key', 'admin.system.config.Config/form')->name('configForm');
        Route::post('config/save/:key', 'admin.system.config.ConfigValue/save')->name('configSave');

        //组合数据
        Route::group('group', function () {
            Route::get('lst', '/lst')->name('groupLst');
            Route::post('create', '/create')->name('groupCreate');
            Route::post('update/:id', '/update')->name('groupUpdate');
            Route::get('create/table', '/createTable')->name('groupCreateForm');
            Route::get('update/table/:id', '/updateTable')->name('groupUpdateForm');
            Route::get('detail/:id', '/get')->name('groupDetail');
            Route::get('data/lst/:groupId', 'Data/lst')->name('groupDataLst');
            Route::get('data/create/table/:groupId', 'Data/createTable')->name('groupDataCreateForm');
            Route::post('data/create/:groupId', 'Data/create')->name('groupDataCreate');
            Route::get('data/update/table/:groupId/:id', 'Data/updateTable')->name('groupDataUpdateForm');
            Route::post('data/update/:groupId/:id', 'Data/update')->name('groupDataUpdate');
            Route::delete('data/delete/:id', 'Data/delete')->name('groupDataDelete');
            Route::post('data/status/:id', 'Data/changeStatus')->name('groupDataChangeStatus');
        })->prefix('admin.system.groupData.Group');

        //附件管理
        Route::group('system/attachment', function () {
            Route::get('lst', '/getList')->name('systemAttachmentLst');
            Route::delete('delete', '/delete')->name('systemAttachmentDelete');
            Route::post('category', '/batchChangeCategory')->name('systemAttachmentBatchChangeCategory');
        })->prefix('admin.system.attachment.Attachment');

        //上传图片
        Route::post('upload/image/:id/:field', 'admin.system.attachment.Attachment/image')->name('uploadImage');

        //附件分类管理
        Route::group('system/attachment/category', function () {
            Route::get('formatLst', '/getFormatList')->name('systemAttachmentCategoryGetFormatList');
            Route::get('create/form', '/createForm')->name('systemAttachmentCategoryCreateForm');
            Route::get('update/form/:id', '/updateForm')->name('systemAttachmentCategoryUpdateForm');
            Route::post('create', '/create')->name('systemAttachmentCategoryCreate');
            Route::post('update/:id', '/update')->name('systemAttachmentCategoryUpdate');
            Route::delete('delete/:id', '/delete')->name('systemAttachmentCategoryDelete');
        })->prefix('admin.system.attachment.AttachmentCategory');

        //权限管理
        Route::group('system/menu', function () {
            Route::get('lst', '/getList')->name('systemMenuGetLst');
            Route::get('create/form', '/createForm')->name('systemMenuCreateForm');
            Route::get('update/form/:id', '/updateForm')->name('systemMenuUpdateForm');
            Route::post('create', '/create')->name('systemMenuCreate');
            Route::post('update/:id', '/update')->name('systemMenuUpdate');
            Route::delete('delete/:id', '/delete')->name('systemMenuDelete');
        })->prefix('admin.system.auth.Menu');


        //商户权限管理
        Route::group('merchant/menu', function () {
            Route::get('lst', '/getList')->name('systemMerchantMenuGetLst')->append(['merchant' => 1]);
            Route::get('create/form', '/createForm')->name('systemMerchantMenuCreateForm')->append(['merchant' => 1]);
            Route::get('update/form/:id', '/updateForm')->name('systemMerchantMenuUpdateForm')->append(['merchant' => 1]);
            Route::post('create', '/create')->name('systemMerchantMenuCreate')->append(['merchant' => 1]);
            Route::post('update/:id', '/update')->name('systemMerchantMenuUpdate')->append(['merchant' => 1]);
            Route::delete('delete/:id', '/delete')->name('systemMerchantMenuDelete')->append(['merchant' => 1]);
        })->prefix('admin.system.auth.Menu');

        //申请列表
        Route::group('merchant/intention', function () {
            Route::get('lst', '/lst')->name('systemMerchantIntentionLst');
            Route::post('status/:id', '/switchStatus')->name('systemMerchantIntentionStatus');
            Route::delete('delete/:id', '/delete')->name('systemMerchantIntentionDelete');
            Route::get('mark/:id/form', '/form')->name('systemMerchantIntentionMarkForm');
            Route::get('status/:id/form', '/statusForm')->name('systemMerchantIntentionStatusForm');
            Route::post('mark/:id', '/mark')->name('systemMerchantIntentionMark');
            Route::post('agree', '/saveAgree')->name('systemMerchantIntentionSaveAgree');
            Route::get('agree', '/getAgree')->name('systemMerchantIntentionGetAgree');
        })->prefix('admin.system.merchant.MerchantIntention');


        //身份规则
        Route::group('system/role', function () {
            Route::get('lst', '/getList')->name('systemRoleGetList');
            Route::post('create', '/create')->name('systemRoleCreate');
            Route::get('create/form', '/createForm')->name('systemRoleCreateForm');
            Route::post('update/:id', '/update')->name('systemRoleUpdate');
            Route::get('update/form/:id', '/updateForm')->name('systemRoleUpdateForm');
            Route::post('status/:id', '/switchStatus')->name('systemRoleStatus');
            Route::delete('delete/:id', '/delete')->name('systemRoleDelete');
        })->prefix('admin.system.auth.Role');


        //Admin管理
        Route::group('system/admin', function () {
            Route::get('lst', '.Admin/getList')->name('systemAdminLst');
            Route::post('status/:id', '.Admin/switchStatus')->name('systemAdminStatus');
            Route::post('create', '.Admin/create')->name('systemAdminCreate');
            Route::get('create/form', '.Admin/createForm')->name('systemAdminCreateForm');
            Route::post('update/:id', '.Admin/update')->name('systemAdminUpdate');
            Route::get('update/form/:id', '.Admin/updateForm')->name('systemAdminUpdateForm');
            Route::post('password/:id', '.Admin/password')->name('systemAdminPassword');
            Route::get('password/form/:id', '.Admin/passwordForm')->name('systemAdminPasswordForm');
            Route::delete('delete/:id', '.Admin/delete')->name('systemAdminDelete');
            Route::get('edit/form', '.Admin/editForm')->name('systemAdminEditForm');
            Route::post('edit', '.Admin/edit')->name('systemAdminEdit');
            Route::get('edit/password/form', '.Admin/editPasswordForm')->name('systemAdminEditPasswordForm');
            Route::post('edit/password', '.Admin/editPassword')->name('systemAdminEditPassword');
            Route::get('log', '.AdminLog/lst')->name('systemAdminLog');
        })->prefix('admin.system.admin');

        //商户管理
        Route::group('system/merchant', function () {
            Route::get('create/form', '.Merchant/createForm')->name('systemMerchantCreateForm');
            Route::get('count', '.Merchant/count')->name('systemMerchantCount');
            Route::get('lst', '.Merchant/lst')->name('systemMerchantLst');
            Route::post('create', '.Merchant/create')->name('systemMerchantCreate');
            Route::get('update/form/:id', '.Merchant/updateForm')->name('systemMerchantUpdateForm');
            Route::post('update/:id', '.Merchant/update')->name('systemMerchantUpdate');
            Route::post('status/:id', '.Merchant/switchStatus')->name('systemMerchantStatus');
            Route::post('close/:id', '.Merchant/switchClose')->name('systemMerchantClose');
            Route::delete('delete/:id', '.Merchant/delete')->name('systemMerchantDelete');
            Route::post('password/:id', '.MerchantAdmin/password')->name('systemMerchantAdminPassword');
            Route::get('password/form/:id', '.MerchantAdmin/passwordForm')->name('systemMerchantAdminPasswordForm');
            Route::post('login/:id', '.Merchant/login')->name('systemMerchantLogin');
            Route::get('changecopy/:id/form', '.Merchant/changeCopyNumForm')->name('systemMerchantChangeCopyForm');
            Route::post('changecopy/:id', '.Merchant/changeCopyNum')->name('systemMerchantChangeCopy');
        })->prefix('admin.system.merchant');

        //商户分类
        Route::group('system/merchant', function () {
            Route::get('category/lst', '/lst')->name('systemMerchantCategoryLst');
            Route::post('category', '/create')->name('systemMerchantCategoryCreate');
            Route::get('category/form', '/createForm')->name('systemMerchantCategoryCreateForm');
            Route::delete('category/:id', '/delete')->name('systemMerchantCategoryDelete');
            Route::post('category/:id', '/update')->name('systemMerchantCategoryUpdate');
            Route::get('category/form/:id', '/updateForm')->name('systemMerchantCategoryUpdateForm');
        })->prefix('admin.system.merchant.MerchantCategory');


        //用户标签
        Route::group('user/label', function () {
            Route::get('lst', '/lst')->name('systemUserLabelLst');
            Route::post('user/label', '/create')->name('systemUserLabelCreate');
            Route::get('form', '/createForm')->name('systemUserLabelCreateForm');
            Route::delete(':id', '/delete')->name('systemUserLabelDelete');
            Route::post(':id', '/update')->name('systemUserLabelUpdate');
            Route::get('form/:id', '/updateForm')->name('systemUserLabelUpdateForm');
        })->prefix('admin.user.UserLabel');


        //用户分组
        Route::group('user/group', function () {
            Route::get('lst', '/lst')->name('systemUserGroupLst');
            Route::post('user/group', '/create')->name('systemUserGroupCreate');
            Route::get('form', '/createForm')->name('systemUserGroupCreateForm');
            Route::delete(':id', '/delete')->name('systemUserGroupDelete');
            Route::post(':id', '/update')->name('systemUserGroupUpdate');
            Route::get('form/:id', '/updateForm')->name('systemUserGroupUpdateForm');
        })->prefix('admin.user.UserGroup');

        //推广人
        Route::group('user/spread', function () {
            Route::get('lst/:uid', '/spreadList')->name('systemUserSpreadLst');
            Route::get('order/:uid', '/spreadOrder')->name('systemUserSpreadOrder');
            Route::post('clear/:uid', '/clearSpread')->name('systemUserSpreadClear');
        })->prefix('admin.user.User');

        //用户
        Route::group('user', function () {
            //用户列表
            Route::get('lst', '/lst')->name('systemUserLst');
            Route::get('promoter/lst', '/promoterList')->name('systemPromoterUserLst');
            Route::get('update/form/:id', '/updateForm')->name('systemUserUpdateForm');
            Route::post('update/:id', '/update')->name('systemUserUpdate');
            Route::get('lst', '/lst')->name('systemUserLst');
            //修改用户分组
            Route::get('change_group/form/:id', '/changeGroupForm')->name('systemUserChangeGroupForm');
            Route::get('batch_change_group/form', '/batchChangeGroupForm')->name('systemUserBatchChangeGroupForm');
            Route::post('change_group/:id', '/changeGroup')->name('systemUserChangeGroup');
            Route::post('batch_change_group', '/batchChangeGroup')->name('systemUserBatchChangeGroup');
            //修改用户标签
            Route::get('change_label/form/:id', '/changeLabelForm')->name('systemUserChangeLabelForm');
            Route::get('batch_change_label/form', '/batchChangeLabelForm')->name('systemUserBatchChangeLabelForm');
            Route::post('change_label/:id', '/changeLabel')->name('systemUserChangeLabel');
            Route::post('batch_change_label', '/batchChangeLabel')->name('systemUserBatchChangeLabel');
            //修改用户余额
            Route::get('change_now_money/form/:id', '/changeNowMoneyForm')->name('systemUserChangeNowMoneyForm');
            Route::post('change_now_money/:id', '/changeNowMoney')->name('systemUserChangeNowMoney');
            //微信图文群发
            Route::post('news/push', '/sendNews')->name('systemWechatUserSendNews');

            Route::get('detail/:id', '/detail')->name('systemUserDetail');
            Route::get('order/:id', '/order')->name('systemUserOrder');
            Route::get('coupon/:id', '/coupon')->name('systemUserCoupon');
            Route::get('bill/:id', '/bill')->name('systemUserBill');

        })->prefix('admin.user.User');

        //内容管理
        Route::group('system/article/category', function () {
            Route::get('create/form', '/createForm')->name('systemArticleCategoryCreateForm');
            Route::get('lst', '/lst')->name('systemArticleCategoryLst');
            Route::post('create', '/create')->name('systemArticleCategoryCreate');
            Route::get('update/form/:id', '/updateForm')->name('systemArticleCategoryUpdateForm');
            Route::post('update/:id', '/update')->name('systemArticleCategoryUpdate');
            Route::post('status/:id', '/switchStatus')->name('systemArticleCategoryStatus');
            Route::delete('delete/:id', '/delete')->name('systemArticleCategoryDelete');
            Route::get('detail/:id', '/detail')->name('systemArticleCategoryDetail');
        })->prefix('admin.article.ArticleCategory');


        Route::group('system/article/article', function () {
            Route::get('lst', '/getList')->name('systemArticlArticleLst');
            Route::post('create', '/create')->name('systemArticleArticleCreate');
            Route::post('update/:id', '/update')->name('systemArticArticleleUpdate');
            Route::delete('delete/:id', '/delete')->name('systemArticArticleleDelete');
            Route::get('detail/:id', '/detail')->name('systemArticArticleleDetail');
            Route::get('lst', '/getList')->name('systemArticlArticleLst');
        })->prefix('admin.article.Article');


        //微信菜单
        Route::group('wechat', function () {
            Route::get('menu', '/info')->name('wechatMenu');
            Route::post('menu', '/save')->name('saveWechatMenu');
        })->prefix('admin.wechat.WechatMenu');


        //自动回复
        Route::group('wechat/reply', function () {
            Route::get('detail/:id', '/info')->name('wechatReplyInfo');
            Route::post('save/:key', '/save')->name('saveWechatReply');
            Route::post('create', '/create')->name('createWechatReply');
            Route::post('update/:id', '/update')->name('updateWechatReply');
            Route::get('lst', '/lst')->name('wechatReplyLst');
            Route::delete(':id', '/delete')->name('wechatReplyDelete');
            Route::post('status/:id', '/changeStatus')->name('wechatReplyStatus');
            Route::post('upload/image', '/uploadImage')->name('wechatUploadImage');
            Route::post('upload/voice', '/uploadVoice')->name('wechatUploadVoice');
        })->prefix('admin.wechat.WechatReply');


        /*//微信用户标签
        Route::group('wechat/user/tag',function(){
            Route::get('/lst', '/lst')->name('wechatUserTagLst');
            Route::get('/create/form', '/createForm')->name('createWechatUserTagForm');
            Route::post('/create', '/create')->name('createWechatUserTag');
            Route::get('/update/form/:id', '/updateForm')->name('updateWechatUserTagForm');
            Route::post('/update/:id', '/update')->name('updateWechatUserTag');
            Route::delete('/delete/:id', '/delete')->name('deleteWechatUserTag');
        })->prefix('admin.wechat.WechatTag');


        //微信用户分组
        Route::group('wechat/user/group',function(){
            Route::get('lst', '/lst')->name('wechatUserGroupLst');
            Route::get('create/form', '/createForm')->name('createWechatUserGroupForm');
            Route::post('create', '/create')->name('createWechatUserGroup');
            Route::get('update/form/:id', '/updateForm')->name('updateWechatUserGroupForm');
            Route::post('update/:id', '/update')->name('updateWechatUserGroupTag');
            Route::delete('delete/:id', '/delete')->name('deleteWechatUserGroup');
        })->prefix('admin.wechat.WechatGroup');


        //微信用户管理
        Route::group('wechat',function(){
            Route::get('user/lst', '/lst')->name('wechatUserLst');
            Route::get('user/sync_tag/:id', '/syncTag')->name('wechatUserSyncTag');
            Route::get('user/tag_group', '/tagGroup')->name('wechatUserTagGroup');
            Route::get('user_tag/form/:id', '/tagForm')->name('wechatUserTagForm');
            Route::post('user_tag/:id', '/tag')->name('wechatUserTag');
            Route::get('user_group/form/:id', '/groupForm')->name('wechatUserGroupForm');
            Route::post('user_group/:id', '/group')->name('wechatUserGroup');
        })->prefix('admin.wechat.WechatUser');*/


        //图文管理
        Route::group('wechat/news', function () {
            Route::post('create', '/create')->name('systemWechatNewsCreate');
            Route::post('update/:id', '/update')->name('systemWechatNewsUpdate');
            Route::delete('delete/:id', '/delete')->name('systemWechatNewsDelete');
            Route::get('lst', '/lst')->name('systemWechatNewsLst');
            Route::post('create', '/create')->name('systemWechatNewsCreate');
            Route::post('update/:id', '/update')->name('systemWechatNewsUpdate');
            Route::delete('delete/:id', '/delete')->name('systemWechatNewsDelete');
            Route::get('detail/:id', '/detail')->name('systemWechatNewsDetail');
        })->prefix('admin.wechat.WechatNews');


        //商品分类
        Route::group('store/category', function () {
            Route::get('create/form', '/createForm')->name('systemStoreCategoryCreateForm');
            Route::get('update/form/:id', '/updateForm')->name('systemStoreCategoryUpdateForm');
            Route::post('update/:id', '/update')->name('systemStoreCategoryUpdate');
            Route::get('lst', '/lst')->name('systemStoreCategoryLst');
            Route::get('detail/:id', '/detail')->name('systemStoreCategoryDtailt');
            Route::post('create', '/create')->name('systemStoreCategoryCreate');
            Route::delete('delete/:id', '/delete')->name('systemStoreCategoryDelete');
            Route::post('status/:id', '/switchStatus')->name('systemStoreCategorySwitchStatus');
            Route::get('list', '/getList');
        })->prefix('admin.store.StoreCategory');


        //品牌分类
        Route::group('store/brand/category', function () {
            Route::get('create/form', '/createForm')->name('systemStoreBrandCategoryCreateForm');
            Route::get('update/form/:id', '/updateForm')->name('systemStoreBrandCategoryUpdateForm');
            Route::post('update/:id', '/update')->name('systemStoreBrandCategoryUpdate');
            Route::get('lst', '/lst')->name('systemStoreBrandCategoryLst');
            Route::get('detail/:id', '/detail')->name('systemStoreBrandCategoryDtailt');
            Route::post('create', '/create')->name('systemStoreBrandCategoryCreate');
            Route::delete('delete/:id', '/delete')->name('systemStoreBrandCategoryDelete');
            Route::post('status/:id', '/switchStatus')->name('systemStoreBrandCategorySwitchStatus');
        })->prefix('admin.store.StoreBrandCategory');


        //品牌
        Route::group('store/brand', function () {
            Route::get('create/form', '/createForm')->name('systemStoreBrandCreateForm');
            Route::get('update/form/:id', '/updateForm')->name('systemStoreBrandUpdateForm');
            Route::get('lst', '/lst')->name('systemStoreBrandLst');
            Route::post('status/:id', '/switchStatus')->name('systemStoreBrandSwithStatus');
            Route::post('create', '/create')->name('systemStoreBrandCreate');
            Route::post('update/:id', '/update')->name('systemStoreBrandUpdate');
            Route::delete('delete/:id', '/delete')->name('systemStoreBrandDelete');
        })->prefix('admin.store.StoreBrand');

        //商品
        Route::group('store/product', function () {
            Route::get('mer_select', '/lists')->name('systemStoreProductMerchantLst');
            Route::get('lst_filter', '/getStatusFilter')->name('systemStoreProductLstFilter');
            Route::get('lst', '/lst')->name('systemStoreProductLst');
            Route::get('list', '/lst');
            Route::get('detail/:id', '/detail')->name('systemStoreProductDetail');
            Route::post('update/:id', '/update')->name('systemStoreProductUpdate');
            Route::post('status', '/switchStatus')->name('systemStoreProductSwitchStatus');
            Route::post('check', '/checkProduct')->name('systemStoreProductCheck');
            Route::post('change/:id', '/changeUsed')->name('systemStoreProductChangeUsed');
        })->prefix('admin.store.StoreProduct');

        //直播间
        Route::group('broadcast/room', function () {
            Route::get('lst', '/lst')->name('systemBroadcastRoomLst');
            Route::get('detail/:id', '/detail')->name('systemBroadcastRoomDetail');
            Route::get('apply/form/:id', '/applyForm')->name('systemBroadcastRoomApplyForm');
            Route::post('apply/:id', '/apply')->name('systemBroadcastRoomApply');
            Route::post('status/:id', '/changeStatus')->name('systemBroadcastRoomChangeStatus');
            Route::post('sort/:id', '/sort')->name('systemBroadcastRoomSort');
            Route::post('live_status/:id', '/changeLiveStatus')->name('systemBroadcastRoomChangeLiveStatus');
            Route::delete('delete/:id', '/delete')->name('systemBroadcastRoomDelete');
            Route::get('goods/:id', '/goodsList')->name('systemBroadcastRoomGoods');
        })->prefix('admin.store.BroadcastRoom');

        //直播间商品
        Route::group('broadcast/goods', function () {
            Route::get('lst', '/lst')->name('systemBroadcastGoodsLst');
            Route::get('detail/:id', '/detail')->name('systemBroadcastGoodsDetail');
            Route::get('apply/form/:id', '/applyForm')->name('systemBroadcastGoodsApplyForm');
            Route::post('apply/:id', '/apply')->name('systemBroadcastGoodsApply');
            Route::post('status/:id', '/changeStatus')->name('systemBroadcastGoodsChangeStatus');
            Route::post('sort/:id', '/sort')->name('systemBroadcastGoodsSort');
            Route::delete('delete/:id', '/delete')->name('systemBroadcastGoodsDelete');
        })->prefix('admin.store.BroadcastGoods');

        //礼包
        Route::group('store/bag', function () {
            Route::get('mer_select', '/lists');
            Route::get('list', '/lst');
            Route::get('lst_filter', '/getBagStatusFilter')->name('systemStoreBagLstFilter');
            Route::get('lst', '/bagList')->name('systemStoreBagLst');
            Route::get('detail/:id', '/detail')->name('systemStoreBagDetail');
            Route::post('update/:id', '/update')->name('systemStoreBagUpdate');
            Route::post('status', '/switchStatus')->name('systemStoreBagSwitchStatus');
            Route::post('change/:id', '/changeUsed')->name('systemStoreBagChangeUsed');
        })->prefix('admin.store.StoreProduct');

        Route::group('store/coupon', function () {
            Route::get('lst', '/lst')->name('systemStoreCouponLst');
            Route::get('issue', '/issue')->name('systemCouponIssue');
            Route::get('detail/:id', '/detail')->name('systemCouponDetail');
        })->prefix('admin.store.Coupon');
        //地址快读公司
        Route::group('store/express', function () {
            Route::get('lst', '/lst')->name('systemExpressLst');
            Route::get('create/form', '/createForm')->name('systemExpressCreateForm');
            Route::post('create', '/create')->name('systemExpressCreate');
            Route::post('status/:id', '/switchStatus')->name('systemExpressSwitchStatus');
            Route::get('detail/:id', '/detail')->name('systemExpressDetail');
            Route::get('update/form/:id', '/updateForm')->name('systemExpressUpdateForm');
            Route::post('update/:id', '/update')->name('systemExpressUpdate');
            Route::delete('delete/:id', '/delete')->name('systemExpressDelete');
        })->prefix('admin.store.Express');

        //秒杀配置管理
        Route::group('seckill/config', function () {
            Route::get('lst', '/lst')->name('systemSeckillConfigLst');
            Route::get('select', '/select');
            Route::get('create/form', '/createForm')->name('systemSeckillConfigCreateForm');
            Route::post('create', '/create')->name('systemSeckillConfigCreate');
            Route::get('update/:id/form', '/updateForm')->name('systemSeckillConfigUpdateForm');
            Route::post('update/:id', '/update')->name('systemSeckillConfigUpdate');
            Route::post('status/:id', '/switchStatus')->name('systemSeckillConfigStatus');
            Route::delete('delete/:id', '/delete')->name('systemSeckillConfigDelete');
        })->prefix('admin.store.StoreSeckill');

        //秒杀商品管理
        Route::group('seckill/product', function () {
            Route::get('mer_select', '/lists');
            Route::get('lst_filter', '/getStatusFilter')->name('systemStoreSeckillProductLstFilter');
            Route::get('lst', '/lst')->name('systemStoreSeckillProductLst');
            Route::get('list', '/lst');
            Route::get('detail/:id', '/detail')->name('systemStoreSeckillProductDetail');
            Route::post('update/:id', '/update')->name('systemStoreSeckillProductUpdate');
            Route::post('status', '/switchStatus')->name('systemStoreSeckillProductSwitchStatus');
            Route::post('change/:id', '/changeUsed')->name('systemStoreSeckillProductChangeUsed');
        })->prefix('admin.store.StoreSeckillProduct');

        //商品评价管理
        Route::group('store/reply', function () {
            Route::get('lst', '/lst')->name('systemProductReplyLst');
            Route::get('create/form/:id?', '/virtualForm')->name('systemProductReplyCreateForm');
            Route::post('create', '/virtualReply')->name('systemProductReplyCreate');
            Route::delete('delete/:id', '/delete')->name('systemProductReplyDelete');
        })->prefix('admin.store.StoreProductReply');

        //安全维护
        Route::group('safety/database', function () {
            Route::get('lst', '/lst')->name('systemSafetyDatabaseLst');
            Route::get('fileList', '/fileList')->name('systemSafetyDatabaseFileList');
            Route::get('detail/:name', '/detail')->name('systemSafetyDatabaseDetail');
            Route::post('backups', '/backups')->name('systemSafetyDatabaseBackups');
            Route::post('optimize', '/optimize')->name('systemSafetyDatabaseOptimize');
            Route::post('repair', '/repair')->name('systemSafetyDatabaseRepair');
            Route::get('download/:feilname', '/downloadFile')->name('systemSafetyDatabaseDownloadFile');
            Route::delete('delete/:feilname', '/deleteFile')->name('systemSafetyDatabaseDeleteFile');
        })->prefix('admin.system.safety.Database');

        //用户反馈
        Route::group('user/feedback', function () {
            Route::get('category/lst', 'FeedBackCategory/lst')->name('systemUserFeedBackCategoryLst');
            Route::get('category/create/form', 'FeedBackCategory/createForm')->name('systemUserFeedBackCategoryCreateForm');
            Route::post('category/create', 'FeedBackCategory/create')->name('systemUserFeedBackCategoryCreate');
            Route::get('category/update/:id/form', 'FeedBackCategory/updateForm')->name('systemUserFeedBackCategoryUpdateForm');
            Route::post('category/update/:id', 'FeedBackCategory/update')->name('systemUserFeedBackCategoryUpdate');
            Route::post('category/status/:id', 'FeedBackCategory/switchStatus')->name('systemUserFeedBackCategorySwitchStatus');
            Route::delete('category/delete/:id', 'FeedBackCategory/delete')->name('systemUserFeedBackCategoryDelete');

            Route::get('lst', 'FeedBack/lst')->name('systemUserFeedBackLst');
            Route::get('detail/:id', 'FeedBack/detail')->name('systemUserFeedBackDetail');
            //Route::post('reply/:id','FeedBack/reply')->name('systemUserFeedBackReply');
            Route::delete('delete/:id', 'FeedBack/delete')->name('systemUserFeedBackDelete');

        })->prefix('admin.user.');

        //商户订单
        Route::group('merchant/order', function () {
            Route::get('lst/:id', 'Order/lst')->name('systemMerchantOrderLst');
            Route::get('mark/:id/form', 'Order/markForm')->name('systemMerchantOrderMarkForm');
            Route::post('mark/:id', 'Order/mark')->name('systemMerchantOrderMark');

            Route::get('refund/lst/:id', 'RefundOrder/lst')->name('systemMerchantRefundOrderLst');
            Route::get('refund/mark/:id/form', 'RefundOrder/markForm')->name('systemMerchantRefundOrderMarkForm');
            Route::post('refund/mark/:id', 'RefundOrder/mark')->name('systemMerchantRefundOrderMark');

            Route::get('reconciliation/lst', 'Reconciliation/lst')->name('systemMerchantReconciliationLst');
            Route::post('reconciliation/create/:id', 'Reconciliation/create')->name('systemMerchantReconciliationCreate');
            Route::post('reconciliation/status/:id', 'Reconciliation/switchStatus')->name('systemMerchantReconciliationSwitchStatus');
            Route::get('reconciliation/:id/order', 'Order/reList')->name('systemMerchantOrderReList');
            Route::get('reconciliation/:id/refund', 'RefundOrder/reList')->name('systemMerchantRefundOrderReList');
            Route::get('reconciliation/mark/:id/form', 'Reconciliation/markForm')->name('systemMerchantReconciliationMarkForm');
            Route::post('reconciliation/mark/:id', 'Reconciliation/mark')->name('systemMerchantReconciliationMark');

        })->prefix('admin.order.');

        //提现
        Route::group('user/extract', function () {
            Route::get('lst', 'UserExtract/lst')->name('systemUserExtractLst');
            Route::post('status/:id', 'UserExtract/switchStatus')->name('systemUserExtractSwitchStatus');
        })->prefix('admin.user.');

        //微信消息模板
        Route::group('wechat/template', function () {
            Route::get('lst', 'TemplateMessage/lst')->name('systemTemplateMessageLst');
            Route::get('create/form', 'TemplateMessage/createform')->name('systemTemplateMessageCreateForm');
            Route::post('create', 'TemplateMessage/create')->name('systemTemplateMessageCreate');
            Route::get('update/:id/form', 'TemplateMessage/updateForm')->name('systemTemplateMessageUpdateForm');
            Route::post('update/:id', 'TemplateMessage/update')->name('systemTemplateMessageUpdate');
            Route::delete('delete/:id', 'TemplateMessage/delete')->name('systemTemplateMessageDelete');
            Route::post('status/:id', 'TemplateMessage/switchStatus')->name('systemTemplateMessageSwitchStatus');
        })->prefix('admin.wechat.');

        //小程序订阅消息
        Route::group('wechat/template/min', function () {
            Route::get('lst', 'TemplateMessage/minList')->name('systemTemplateMessageMinList');
            Route::get('create/form', 'TemplateMessage/createMinform')->name('systemTemplateMessageMinCreateForm');
            Route::post('create', 'TemplateMessage/create')->name('systemTemplateMessageMinCreate');
            Route::get('update/:id/form', 'TemplateMessage/updateForm')->name('systemTemplateMessageMinUpdateForm');
            Route::post('update/:id', 'TemplateMessage/update')->name('systemTemplateMessageMinUpdate');
            Route::delete('delete/:id', 'TemplateMessage/delete')->name('systemTemplateMessageMinDelete');
            Route::post('status/:id', 'TemplateMessage/switchStatus')->name('systemTemplateMessageMinSwitchStatus');
        })->prefix('admin.wechat.');

        //充值
        Route::group('user/recharge', function () {
            Route::get('list', 'UserRecharge/getList')->name('systemUserRechargeList');
            Route::get('total', 'UserRecharge/total')->name('systemUserRechargeTotal');
        })->prefix('admin.user.');

        //余额变动记录
        Route::group('bill', function () {
            Route::get('list', 'UserBill/getList')->name('systemUserBillList');
            Route::get('type', 'UserBill/type');
        })->prefix('admin.user.');

        //资金明细
        Route::group('financial_record', function () {
            Route::get('list', '/lst')->name('systemFinancialRecordList');
            Route::get('export', '/export')->name('systemFinancialRecordExport');
        })->prefix('admin.system.merchant.FinancialRecord');

        //客服
//        Route::group('service',function(){
//            Route::get('lst/:id','/lst')->name('systemServiceLst');
//            Route::get('mer/:id/user','/merchantUserList')->name('systemServiceMerchantUserLst');
//            Route::get('/:id/:uid/log','/getUserMsnByMerchant')->name('systemServiceMerchantUserLogLst');
//        })->prefix('admin.store.StoreService');

        Route::group('statistics', function () {
            Route::get('main', '/main')->name('systemStatisticsMain');
            Route::get('order', '/order')->name('systemStatisticsOrder');
            Route::get('order_num', '/orderNum')->name('systemStatisticsOrderNum');
            Route::get('order_user', '/orderUser')->name('systemStatisticsOrderUser');
            Route::get('merchant_stock', '/merchantStock')->name('systemStatisticsMerchantStock');
            Route::get('merchant_rate', '/merchantRate')->name('systemStatisticsMerchantRate');
            Route::get('merchant_visit', '/merchantVisit')->name('systemStatisticsMerchantVisit');
            Route::get('user_data', '/userData')->name('systemStatisticsUserData');
        })->prefix('admin.Common');
        Route::get('statistics/user', 'merchant.Common/user')->name('systemStatisticsUser');
        Route::get('statistics/user_rate', 'merchant.Common/userRate')->name('systemStatisticsUserRate');

        //所有订单
        Route::group('order', function () {
            Route::get('filtter', 'Order/orderType')->name('systemStoreOrderFiltter');
            Route::get('lst', 'Order/getAllList')->name('systemOrderLst');
            Route::get('takelst', 'Order/getTakeList')->name('systemTakeOrderLst');
            Route::get('express/:id', 'Order/express')->name('systemOrderExpress');
            Route::get('chart', 'Order/chart')->name('systemOrderTitle');
            Route::get('takechart', 'Order/takeChart')->name('systemTakeOrderTitle');
            Route::get('detail/:id', 'Order/detail')->name('systemOrderDetail');
            Route::get('refund/lst', 'RefundOrder/getAllList')->name('systemRefundOrderLst');
            Route::get('excel', 'Order/Excel')->name('systemOrderExcel');
        })->prefix('admin.order.');
        Route::get('system/city/lst', 'merchant.store.shipping.City/lst');
        //退出登陆
        Route::get('logout', 'admin.system.admin.Login/logout');
        //授权
        Route::post('auth_apply', 'admin.Common/auth_apply');
        Route::get('check_auth', 'admin.Common/check_auth');
        Route::get('auth', 'admin.Common/auth');
        Route::get('upload/config', 'admin.Common/uploadConfig')->name('systemUploadConfig');
        Route::post('upload/config', 'admin.Common/saveUploadConfig')->name('systemSaveUploadConfig');
        //导出文件
        Route::get('excel/lst', 'merchant.store.Excel/lst')->name('systemStoreExcelLst');
        Route::get('excel/download/:id', 'merchant.store.Excel/download')->name('systemStoreExcelDownload');
    })->middleware(AllowOriginMiddleware::class)
        ->middleware(AdminTokenMiddleware::class, true)
        ->middleware(AdminAuthMiddleware::class)
        ->middleware(LogMiddleware::class);

//不带token认证
    Route::group(function () {

        //短信支付成功回调
        Route::any('sms/notice', 'admin.system.sms.SmsPay/notice');

        //验证码
        Route::get('captcha', 'admin.system.admin.Login/getCaptcha');
        //登录
        Route::post('login', 'admin.system.admin.Login/login');

        Route::get('login_config', 'admin.Common/loginConfig');

        Route::group(function () {

        })->middleware(AdminTokenMiddleware::class, false);

    })->middleware(AllowOriginMiddleware::class);

    Route::miss(function () {
        return app('json')->fail('接口不存在');
    })->middleware(AllowOriginMiddleware::class);
})->middleware(InstallMiddleware::class);
