<template>
  <div>
    <div v-if="psInfo" class="acea-row row-middle">
      <div class="avatar mr15"><div class="block"><el-avatar :size="50" :src="psInfo.avatar ? psInfo.avatar : moren"></el-avatar></div></div>
      <div class="dashboard-workplace-header-tip">
        <p class="dashboard-workplace-header-tip-title" v-text="psInfo.nickname || '-'" />
        <div class="dashboard-workplace-header-tip-desc">
          <span class="dashboard-workplace-header-tip-desc-sp">余额: {{ psInfo.now_money }}</span>
          <span class="dashboard-workplace-header-tip-desc-sp">总计订单: {{ psInfo.pay_count }}</span>
          <span class="dashboard-workplace-header-tip-desc-sp">总消费金额: {{ psInfo.pay_price }}</span>
          <span class="dashboard-workplace-header-tip-desc-sp">本月订单: {{ psInfo.total_pay_count }}</span>
          <span class="dashboard-workplace-header-tip-desc-sp">本月消费金额: {{ psInfo.total_pay_price }}</span>
        </div>
      </div>
    </div>
    <el-row align="middle" :gutter="10" class="ivu-mt mt20">
      <el-col :span="4">
        <el-menu
          default-active="0"
          class="el-menu-vertical-demo"
          @select="changeType"
        >
          <el-menu-item v-for="(item, index) in list" :key="index" :name="item.val" :index="item.val">
            <span slot="title">{{ item.label }}</span>
          </el-menu-item>
        </el-menu>
      </el-col>
      <el-col :span="20">
        <el-table v-loading="loading" :data="tableData.data" class="tabNumWidth" size="mini">
          <el-table-column
            v-for="(item, index) in columns"
            :key="index"
            :prop="item.key"
            :label="item.title"
            width="item.minWidth"
          />
          <el-table-column v-if="type === '3'" label="有效期" min-width="150">
            <template slot-scope="scope">
              <span>{{ scope.row ? scope.row.start_time + '-' + scope.row.end_time: '' | filterEmpty}}</span>
            </template>
          </el-table-column>
        </el-table>
        <div class="block">
          <el-pagination
            :page-sizes="[6, 12, 18, 24]"
            :page-size="tableFrom.limit"
            :current-page="tableFrom.page"
            layout="total, sizes, prev, pager, next, jumper"
            :total="tableData.total"
            @size-change="handleSizeChange"
            @current-change="pageChange"
          />
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { userOrderApi, userDetailApi, userCouponApi, userBillApi } from '@/api/user'
export default {
  name: 'UserDetails',
  props: {
    uid: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      moren: require("@/assets/images/f.png"),
      loading: false,
      columns: [],
      Visible: false,
      list: [
        { val: '0', label: '消费记录' },
        { val: '3', label: '持有优惠券' },
        { val: '4', label: '余额变动' }
      ],
      tableData: {
        data: [],
        total: 0
      },
      tableFrom: {
        page: 1,
        limit: 6
      },
      psInfo: null,
      type: '0'
    }
  },
  mounted() {
    if (this.uid) {
      this.getHeader()
      this.getInfo('0')
    }
  },
  methods: {
    changeType(key) {
      this.type = key
      this.tableFrom.page = 1
      this.getInfo(key)
    },
    getInfo(key) {
      this.loading = true
      switch (key) {
        case '0':
          userOrderApi(this.uid, this.tableFrom).then(res => {
            this.tableData.data = res.data.list
            this.tableData.total = res.data.count
            this.columns = [
              {
                title: '订单ID',
                key: 'order_id',
                minWidth: 250
              },
              {
                title: '收货人',
                key: 'real_name',
                minWidth: 90
              },
              {
                title: '商品数量',
                key: 'total_num',
                minWidth: 80
              },
              {
                title: '商品总价',
                key: 'total_price',
                minWidth: 90
              },
              {
                title: '实付金额',
                key: 'pay_price',
                minWidth: 90
              },
              {
                title: '交易完成时间',
                key: 'pay_time',
                minWidth: 160
              }
            ]
            this.loading = false
          }).catch(() => {
            this.loading = false
          })
          break
        case '3':
          userCouponApi(this.uid, this.tableFrom).then(res => {
            this.tableData.data = res.data.list
            this.tableData.total = res.data.count
            this.columns = [
              {
                title: '优惠券名称',
                key: 'coupon_title',
                minWidth: 120
              },
              {
                title: '面值',
                key: 'coupon_price',
                minWidth: 120
              },
              // {
              //   title: '有效期',
              //   key: 'add_time',
              //   minWidth: 120
              // },
              {
                title: '最低消费额',
                key: 'use_min_price',
                minWidth: 120
              },
              {
                title: '兑换时间',
                key: 'use_time',
                minWidth: 120
              }
            ]
            this.loading = false
          }).catch(() => {
            this.loading = false
          })
          break
        default:
          userBillApi(this.uid, this.tableFrom).then(res => {
            this.tableData.data = res.data.list
            this.tableData.total = res.data.count
            this.columns = [
              {
                title: '变动金额',
                key: 'number',
                minWidth: 90
              },
              {
                title: '变动后',
                key: 'balance',
                minWidth: 90
              },
              {
                title: '类型',
                key: 'title',
                minWidth: 100
              },
              {
                title: '创建时间',
                key: 'create_time',
                minWidth: 150
              },
              {
                title: '备注',
                key: 'mark',
                minWidth: 200
              }
            ]
            this.loading = false
          }).catch(() => {
            this.loading = false
          })
      }
    },
    pageChange(page) {
      this.tableFrom.page = page
      this.getInfo(this.type)
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val
      this.getInfo(this.type)
    },
    getHeader() {
      userDetailApi(this.uid).then(res => {
        this.psInfo = res.data
      })
    }
  }
}
</script>

<style scoped lang="scss">
  .avatar{
    width: 60px;
    height: 60px;
    margin-left: 18px;
    img{
      width: 100%;
      height: 100%;
    }
  }
  .dashboard-workplace {
    &-header {
      &-avatar {
        /*width: 64px;*/
        /*height: 64px;*/
        /*border-radius: 50%;*/
        margin-right: 16px;
        font-weight: 600;
      }

      &-tip {
        width: 82%;
        display: inline-block;
        vertical-align: middle;
        margin-top: -12px;
        &-title {
          font-size: 13px;
          color: #000000;
          margin-bottom: 12px;
        }

        &-desc {
          &-sp {
            width: 32%;
            color: #17233D;
            font-size: 13px;
            display: inline-block;
          }
        }
      }

      &-extra {
        .ivu-col {
          p {
            text-align: right;
          }

          p:first-child {
            span:first-child {
              margin-right: 4px;
            }

            span:last-child {
              color: #808695;
            }
          }

          p:last-child {
            font-size: 22px;
          }
        }
      }
    }
  }
</style>
