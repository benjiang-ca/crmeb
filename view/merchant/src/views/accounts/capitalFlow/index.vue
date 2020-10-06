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
            />
            <div class="mt20">
              <span class="seachTiele">关键字：</span>
              <el-input
                v-model="tableFrom.keyword"
                placeholder="请输入订单号/用户昵称"
                class="selWidth mr20"
              />
              <el-button size="small" type="primary" icon="el-icon-search" @click="getList">搜索</el-button>
              <el-button size="small" type="primary" icon="el-icon-top" @click="exportRecord">生成列表</el-button>
              <el-button size="small" type="primary" @click="getExportFileList">导出已生成列表</el-button>
            </div>
          </el-form>
        </div>
      </div>
      <el-table v-loading="listLoading" :data="tableData.data" style="width: 100%" size="mini">
        <el-table-column label="订单号" min-width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.financial_type != 'mer_accoubts'">{{ scope.row.order_sn }}</span>
            <span v-else>{{ scope.row.financial_record_sn }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="financial_record_sn" label="交易流水号" min-width="100" />
        <el-table-column prop="create_time" label="交易时间" min-width="100" sortable />
        <el-table-column prop="user_info" label="对方信息" min-width="80" />
        <el-table-column label="交易类型" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.financial_type | transactionTypeFilter }}</span>
          </template>
        </el-table-column>
        <el-table-column label="收支金额（元）" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.financial_type == 'refund_order' ? -scope.row.number : scope.row.number }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="150" fixed="right">
          <template slot-scope="scope">
            <router-link v-if="scope.row.financial_type == 'mer_accoubts'" :to=" { path:`${roterPre}` + '/accounts/reconciliation?reconciliation_id='+scope.row.order_id } ">
              <el-button type="text" size="small" class="mr10">详情</el-button>
            </router-link>
            <router-link v-else-if="scope.row.financial_type == 'order' || scope.row.financial_type == 'brokerage_one' || scope.row.financial_type == 'brokerage_two'" :to=" { path:`${roterPre}` + '/order/list?order_sn='+scope.row.order_sn } ">
              <el-button type="text" size="small" class="mr10">详情</el-button>
            </router-link>
            <router-link v-else :to=" { path:`${roterPre}` + '/order/refund?refund_order_sn='+scope.row.order_sn } ">
              <el-button type="text" size="small" class="mr10">详情</el-button>
            </router-link>
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
    <!--导出订单列表-->
    <file-list ref="exportList" />
  </div>
</template>

<script>
import { capitalFlowLstApi, capitalFlowExportApi } from "@/api/accounts";
import fileList from "@/components/exportFile/fileList";
import { roterPre } from '@/settings'

export default {
  components: { fileList },
  data() {
    return {
      tableData: {
        data: [],
        total: 0,
      },
      roterPre: roterPre,
      listLoading: true,
      tableFrom: {
        keyword: "",
        date: "",
        page: 1,
        limit: 20,
      },
      timeVal: [],
      fromList: {
        title: "选择时间",
        custom: true,
        fromTxt: [
          { text: "全部", val: "" },
          { text: "今天", val: "today" },
          { text: "昨天", val: "yesterday" },
          { text: "最近7天", val: "lately7" },
          { text: "最近30天", val: "lately30" },
          { text: "本月", val: "month" },
          { text: "本年", val: "year" },
        ],
      },
      selectionList: [],
      ids: "",
      tableFromLog: {
        page: 1,
        limit: 10,
      },
      tableDataLog: {
        data: [],
        total: 0,
      },
      LogLoading: false,
      dialogVisible: false,
      evaluationStatusList: [
        { value: 1, label: "已回复" },
        { value: 0, label: "未回复" },
      ],
      orderDatalist: null,
    };
  },
  mounted() {
    this.getList();
  },
  methods: {
    // 选择时间
    selectChange(tab) {
      this.tableFrom.date = tab;
      this.timeVal = [];
      this.getList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.tableFrom.date = e ? this.timeVal.join("-") : "";
      this.getList();
    },
    // 导出
    exportRecord() {
      capitalFlowExportApi()
        .then((res) => {
          /*this.$message.success(res.message);
          this.$refs.exportList.exportFileList();*/
          const h = this.$createElement;
          this.$msgbox({
            title: '提示',
            message: h('p', null, [
              h('span', null, '文件正在生成中，请稍后点击"'),
              h('span', { style: 'color: teal' }, '导出已生成列表'),
              h('span', null, '"查看~ '),
            ]),
            confirmButtonText: '我知道了',
          }).then(action => {

          });
        })
        .catch((res) => {
          this.$message.error(res.message);
        });
    },
    // 导出列表
    getExportFileList() {
      this.$refs.exportList.exportFileList();
    },
    // 列表
    getList() {
      this.listLoading = true;
      capitalFlowLstApi(this.tableFrom)
        .then((res) => {
          this.tableData.data = res.data.list;
          this.tableData.total = res.data.count;
          this.listLoading = false;
        })
        .catch((res) => {
          this.$message.error(res.message);
          this.listLoading = false;
        });
    },
    pageChange(page) {
      this.tableFrom.page = page;
      this.getList();
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val;
      this.getList();
    },
  },
};
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
.demo-image__preview {
  position: relative;
  padding-left: 40px;
}
.demo-image__preview .el-image,
.el-image__error {
  position: absolute;
  left: 0;
}
.maxw180 {
  display: inline-block;
  max-width: 180px;
}
</style>
