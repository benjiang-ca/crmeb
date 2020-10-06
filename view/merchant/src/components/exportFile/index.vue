<template>
  <div v-if="fileVisible" title="导出订单列表" :visible.sync="fileVisible" width="900px">
    <div v-loading="loading">
      <el-table
        v-loading="loading"
        :data="tableData.data"
        style="width: 100%"
        size="mini"
        class="table"
        highlight-current-row
      >
        <el-table-column label="文件名" prop="name" min-width="170" />
        <el-table-column label="操作者ID" prop="admin_id" min-width="170" />
        <el-table-column label="订单类型" min-width="170">
          <template>
            <span style="display: block;">订单</span>
          </template>
        </el-table-column>
        <el-table-column label="状态" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.status | exportOrderStatusFilter }}</span>
          </template>
        </el-table-column>
        <el-table-column key="8" label="操作" min-width="150" fixed="right" align="center">
          <template slot-scope="scope">
            <el-button
              v-if="scope.row.status == 1 "
              type="text"
              size="small"
              class="mr10"
              @click="downLoad(scope.row.excel_id)"
            >下载</el-button>
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
    </div>
  </div>
</template>
<script>
import { exportFileLstApi, downloadFileApi } from "@/api/order";
export default {
  name: "FileList",
  data() {
    return {
      fileVisible: true,
      loading: false,
      tableData: {
        data: [],
        total: 0,
      },
      tableFrom: {
        page: 1,
        limit: 20,
      },
    };
  },
  methods: {
    exportFileList() {
      this.loading = true;
      exportFileLstApi()
        .then((res) => {
          this.fileVisible = true;
          this.tableData.data = res.data.list;
          this.tableData.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.$message.error(res.message);
          this.listLoading = false;
        });
    },
    // 下载
    downLoad(id) {
      downloadFileApi()
        .then(({ message }) => {
        })
        .catch(({ message }) => {
          this.$message.error(message);
        });
    },
    pageChange(page) {
      this.tableFrom.page = page;
      this.getList();
    },
    pageChangeLog(page) {
      this.tableFromLog.page = page;
      this.getList();
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val;
      this.getList();
    },
  },
};
</script>

<style scoped lang="scss">
.title {
  margin-bottom: 16px;
  color: #17233d;
  font-weight: 500;
  font-size: 14px;
}
.description {
  &-term {
    display: table-cell;
    padding-bottom: 10px;
    line-height: 20px;
    width: 50%;
    font-size: 12px;
  }
}
</style>
