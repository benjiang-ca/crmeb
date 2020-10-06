<template>
  <el-dialog
    v-if="orderDatalist"
    v-loading="loading"
    title="退款单信息"
    :visible.sync="dialogVisible"
    width="700px"
  >
    <div class="description">
      <div class="title">用户信息</div>
      <div class="acea-row">
        <div class="description-term">用户昵称：</div>
        <div class="description-term">联系电话：{{ orderDatalist.user_phone }}</div>
      </div>
      <el-divider />
      <div class="title">退款单信息</div>
      <div class="acea-row">
        <div class="description-term">退款单号：{{ orderDatalist.refund_order_sn }}</div>
        <div class="description-term">订单状态：{{ orderDatalist.status | orderRefundFilter }}</div>
        <div class="description-term">商品总数：{{ orderDatalist.refund_num }}</div>
        <!--<div class="description-term">商品总价：{{ orderDatalist }}</div>-->
        <div class="description-term">退款金额：{{ orderDatalist.refund_price }}</div>
        <!--<div class="description-term">用户备注：{{ orderDatalist.coupon_price }}</div>-->
        <div class="description-term">创建时间：{{ orderDatalist.create_time }}</div>
        <div class="description-term">商家备注：{{ orderDatalist.mer_mark }}</div>
      </div>
    </div>
  </el-dialog>
</template>

<script>
export default {
  name: 'OrderDetail',
  props: {
    orderDatalist: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      dialogVisible: false,
      // orderDatalist: null,
      loading: false
    }
  },
  mounted() {
  },
  methods: {
  }
}
</script>

<style scoped lang="scss">
  .title{
    margin-bottom: 16px;
    color: #17233d;
    font-weight: 500;
    font-size: 14px;
  }
  .description{
    &-term {
      display: table-cell;
      padding-bottom: 10px;
      line-height: 20px;
      width: 50%;
      font-size: 12px;
    }
  }

</style>
