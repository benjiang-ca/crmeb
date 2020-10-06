<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <div class="container">
          <el-form size="small" label-width="100px">
            <span class="seachTiele">时间选择：</span>
            <el-radio-group
              v-model="tableFrom.date"
              type="button"
              class="mr20"
              size="small"
              @change="selectChange(tableFrom.date)"
              clearable
            >
              <el-radio-button
                v-for="(item,i) in fromList.fromTxt"
                :key="i"
                :label="item.val"
              >{{ item.text }}</el-radio-button>
            </el-radio-group>
            <el-date-picker
              v-model="timeVal"
              value-format="yyyy/MM/dd"
              format="yyyy/MM/dd"
              size="small"
              type="daterange"
              placement="bottom-end"
              placeholder="自定义时间"
              style="width: 250px;"
              @change="onchangeTime"
              clearable
            />
            <div class="mt20">
              <span class="seachTiele">评价分类：</span>
              <el-select
                v-model="tableFrom.is_reply"
                placeholder="请选择"
                class="filter-item selWidth mr20"
                @change="getList(1)"
                clearable
              >
                <el-option
                  v-for="item in evaluationStatusList"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                />
              </el-select>
              <span class="seachTiele">商品信息：</span>
              <el-input
                v-model="tableFrom.keyword"
                placeholder="请输入商品ID或者商品信息"
                class="selWidth mr20"
                clearable
              />
              <span class="seachTiele">用户名称：</span>
              <el-input v-model="tableFrom.nickname" placeholder="请输入用户名称" class="selWidth mr20" />
              <el-button size="small" type="primary" icon="el-icon-search" @click="getList(1)">搜索</el-button>
            </div>
          </el-form>
        </div>
      </div>
      <el-table
        v-loading="listLoading"
        :data="tableData.data"
        style="width: 100%"
        size="mini"
      >
        <el-table-column
          prop="product_id"
          label="商品ID"
          min-width="50"
        />
        <el-table-column label="商品信息" min-width="180">
          <template slot-scope="scope">
            <div class="tabBox acea-row row-middle">
              <div class="demo-image__preview">
                <el-image
                  :src="scope.row.image"
                  :preview-src-list="[scope.row.image]"
                />

              </div>
              <span class="tabBox_tit">{{ scope.row.store_name }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column
          prop="nickname"
          label="用户名称"
          min-width="80"
        />
        <el-table-column
          prop="product_score"
          label="产品评分"
          min-width="50"
          sortable
        />
        <el-table-column
          prop="service_score"
          label="服务评分"
          min-width="50"
          sortable
        />
        <el-table-column
          prop="postage_score"
          label="物流评分"
          min-width="50"
          sortable
        />
        <el-table-column
          prop="comment"
          label="	评价内容"
          min-width="90"
        />
        <el-table-column
          prop="merchant_reply_content"
          label="回复内容"
          min-width="100"
        />
        <el-table-column
          prop="create_time"
          label="评价时间"
          min-width="100"
          sortable
        />
        <el-table-column label="操作" min-width="80" fixed="right">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="handleReply(scope.row.reply_id)">回复</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination
          :page-sizes="[20, 40, 60, 80]"
          :page-size="tableFrom.limit"
          :current-page="tableFrom.page"
          layout="total, sizes, prev, pager, next, jumper"
          :total="tableData.total"
          @size-change="handleSizeChange"
          @current-change="pageChange"
        />
      </div>
    </el-card>
    <!--详情-->
  </div>
</template>

<script>
import { reviewLstApi, reviewReplyApi } from '@/api/product'
export default {
  data() {
    return {
      tableData: {
        data: [],
        total: 0
      },
      listLoading: true,
      tableFrom: {
        is_reply: '',
        nickname: '',
        keyword: '',
        order_sn: '',
        status: '',
        date: '',
        page: 1,
        limit: 20
      },
      timeVal: [],
      fromList: {
        title: '选择时间',
        custom: true,
        fromTxt: [
          { text: '全部', val: '' },
          { text: '今天', val: 'today' },
          { text: '昨天', val: 'yesterday' },
          { text: '最近7天', val: 'lately7' },
          { text: '最近30天', val: 'lately30' },
          { text: '本月', val: 'month' },
          { text: '本年', val: 'year' }
        ]
      },
      selectionList: [],
      ids: '',
      tableFromLog: {
        page: 1,
        limit: 10
      },
      tableDataLog: {
        data: [],
        total: 0
      },
      LogLoading: false,
      dialogVisible: false,
      evaluationStatusList: [
        { value: '', label: '全部' },
        { value: 1, label: '已回复' },
        { value: 0, label: '未回复' }
      ],
      orderDatalist: null
    }
  },
  mounted() {
    this.getList(1)
  },
  methods: {
    // 回复
    handleReply(id) {
      this.$modalForm(reviewReplyApi(id)).then(() => this.getList(1))
    },
    handleSelectionChange(val) {
      this.selectionList = val
      const data = []
      this.selectionList.map(item => {
        data.push(item.id)
      })
      this.ids = data.join(',')
    },
    // 选择时间
    selectChange(tab) {
      this.tableFrom.date = tab
      this.timeVal = []
      this.getList(1)
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e
      this.tableFrom.date = e ? this.timeVal.join('-') : ''
      this.getList(1)
    },
    // 列表
    getList(num) {
      this.listLoading = true
      this.tableFrom.page = num ? num : this.tableFrom.page;
      reviewLstApi(this.tableFrom)
        .then(res => {
          this.tableData.data = res.data.list
          this.tableData.total = res.data.count
          this.listLoading = false
        })
        .catch(res => {
          this.$message.error(res.message)
          this.listLoading = false
        })
    },
    pageChange(page) {
      this.tableFrom.page = page
      this.getList('')
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val
      this.getList('')
    }
  }
}
</script>

<style lang="scss" scoped>
.selWidth {
  width: 300px;
}
.el-dropdown-link {
  cursor: pointer;
  color: #409eff;
  font-size: 12px;
}
.el-icon-arrow-down {
  font-size: 12px;
}
.tabBox_tit {
  width: 60%;
  font-size: 12px !important;
  margin: 0 2px 0 10px;
  letter-spacing: 1px;
  padding: 5px 0;
  box-sizing: border-box;
}
.mt20 {
  margin-top: 20px;
}
.demo-image__preview{
  position: relative;
}
.maxw180{
  display: inline-block;
  max-width: 180px;
}

</style>
