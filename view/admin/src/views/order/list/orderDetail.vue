<template>
  <div>
    <el-dialog
      title="订单信息"
      :visible.sync="dialogVisible"
      width="700px"
      v-if="dialogVisible"
    >
      <div v-loading="loading">
        <div class="description" v-if="orderDatalist">
          <div class="title">收货信息</div>
          <div class="acea-row">
            <div class="description-term">用户昵称：{{orderDatalist.user.nickname}}</div>
            <div class="description-term">收货人：{{ orderDatalist.real_name }}</div>
            <div class="description-term">联系电话：{{ orderDatalist.user_phone }}</div>
            <div class="description-term">收货地址：{{ orderDatalist.user_address }}</div>
          </div>
          <el-divider />
          <div class="title">订单信息</div>
          <div class="acea-row">
            <div class="description-term">订单编号：{{ orderDatalist.order_sn }}</div>
            <div class="description-term">订单状态：{{ orderDatalist.status | orderStatusFilter }}</div>
            <div class="description-term">商品总数：{{ orderDatalist.total_num }}</div>
            <div class="description-term">商品总价：{{ orderDatalist.total_price }}</div>
            <div class="description-term">交付邮费：{{ orderDatalist.total_postage }}</div>
            <div class="description-term">优惠券金额：{{ orderDatalist.coupon_price }}</div>
            <div class="description-term">实际支付：{{ orderDatalist.pay_price }}</div>
            <div class="description-term">创建时间：{{ orderDatalist.create_time }}</div>
            <div class="description-term">支付方式：{{ orderDatalist.pay_type | payTypeFilter }}</div>
            <div class="description-term">商家备注：{{ orderDatalist.remark }}</div>
          </div>
          <template v-if="orderDatalist.delivery_type === '1'">
            <el-divider />
            <div class="title">物流信息</div>
            <div class="acea-row">
              <div class="description-term">快递公司：{{ orderDatalist.delivery_name }}</div>
              <div class="description-term">
                快递单号：{{ orderDatalist.delivery_id }}
                <el-button type="primary" size="mini" style="margin-left: 5px" @click="openLogistics">物流查询</el-button>
              </div>
            </div>
          </template>
          <template v-if="orderDatalist.delivery_type === '2'">
            <el-divider />
            <div class="title">配送信息</div>
            <div class="acea-row">
              <div class="description-term">送货人姓名：{{ orderDatalist.delivery_name }}</div>
              <div class="description-term">送货人电话：{{ orderDatalist.delivery_id }}</div>
            </div>
          </template>
          <template v-if="orderDatalist.mark">
            <el-divider />
            <div class="title">用户备注</div>
            <div class="acea-row">
              <div class="description-term">{{ orderDatalist.mark }}</div>
            </div>
          </template>
        </div>
      </div>
    </el-dialog>
    <el-dialog
      title="物流查询"
      :visible.sync="dialogLogistics"
      width="350px"
      :before-close="handleClose"
      v-if="dialogLogistics"
    >
      <div class="logistics acea-row row-top">
        <div class="logistics_img"><img src="@/assets/images/expressi.jpg"></div>
        <div class="logistics_cent">
          <span>物流公司：{{ orderDatalist.delivery_name }}</span>
          <span>物流单号：{{ orderDatalist.delivery_id }}</span>
        </div>
      </div>
      <div class="acea-row row-column-around trees-coadd">
        <div class="scollhide">
          <el-timeline>
            <el-timeline-item v-for="(item,i) in result" :key="i">
              <p class="time" v-text="item.time" />
              <p class="content" v-text="item.status" />
            </el-timeline-item>
          </el-timeline>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getExpress, orderDetailApi } from '@/api/order'
export default {
  name: 'OrderDetail',
  data() {
    return {
      dialogVisible: false,
      dialogLogistics: false,
      loading: false,
      result: [],
      orderDatalist: null
    }
  },
  mounted() {
  },
  methods: {
    onOrderDetails(id) {
      this.loading = true
      orderDetailApi(id).then(res => {
        this.orderDatalist = res.data
        this.loading = false
      }).catch(({ message }) => {
        this.loading = false
        this.$message.error(message)
      })
    },
    openLogistics() {
      this.getOrderData()
      this.dialogLogistics = true
    },
    handleClose() {
      this.dialogLogistics = false
    },
    // 获取订单物流信息
    getOrderData() {
      getExpress(this.orderDatalist.order_id).then(async res => {
        this.result = res.data
      }).catch(res => {
        this.$message.error(res.message)
      })
    }
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
.logistics{
  align-items: center;
  padding: 10px 0px;
  .logistics_img{
    width: 45px;
    height: 45px;
    margin-right: 12px;
    img{
      width: 100%;
      height: 100%;
    }
  }
  .logistics_cent{
    span{
      display: block;
      font-size: 12px;
    }
  }
}
.trees-coadd{
  width: 100%;
  height: 400px;
  border-radius: 4px;
  overflow: hidden;
  .scollhide{
    width: 100%;
    height: 100%;
    overflow: auto;
    margin-left: 18px;
    padding: 10px 0 10px 0;
    box-sizing: border-box;
    .content{
      font-size: 12px;
    }
    .time{
      font-size: 12px;
      color: #2d8cf0;
    }
  }
}
.scollhide::-webkit-scrollbar {
  display: none; /* Chrome Safari */
}

</style>
