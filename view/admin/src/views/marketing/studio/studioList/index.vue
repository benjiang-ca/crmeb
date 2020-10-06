<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <div class="container">
          <el-form size="small" label-width="100px">
            <el-form-item label="状态：">
              <el-radio-group v-model="tableFrom.status_tag" type="button" @change="getList(1)">
                <el-radio-button label>全部</el-radio-button>
                <el-radio-button label="0">待审核</el-radio-button>
                <el-radio-button label="1">审核已通过</el-radio-button>
                <el-radio-button label="-1">审核未通过</el-radio-button>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="商户类别：" style="display: inline-block;">
              <el-select
                v-model="tableFrom.is_trader"
                clearable
                placeholder="请选择"
                class="selWidth"
                @change="getList(1)"
              >
                <el-option label="自营" value="1" />
                <el-option label="非自营" value="0" />
              </el-select>
            </el-form-item>
            <el-form-item label="关键字：" class="width100" style="display: inline-block;">
              <el-input
                v-model="tableFrom.keyword"
                placeholder="请输入直播间名称/ID/主播昵称/微信号"
                class="selWidth"
                size="small"
              >
                <el-button slot="append" icon="el-icon-search" @click="getList(1)" />
              </el-input>
            </el-form-item>
          </el-form>
        </div>
      </div>
      <el-table
        v-loading="listLoading"
        :data="tableData.data"
        style="width: 100%"
        size="small"
        highlight-current-row
      >
        <el-table-column label="序号" min-width="60">
          <template scope="scope">
            <span>{{ scope.$index+(tableFrom.page - 1) * tableFrom.limit + 1 }}</span>
          </template>
        </el-table-column>
        <el-table-column label="商户名称" min-width="90">
          <template scope="scope">
            <span>{{ scope.row.merchant ? scope.row.merchant.mer_name : '' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="mer_name" label="商户类别" min-width="90">
          <template slot-scope="scope">
            <span v-if="scope.row.merchant" class="spBlock">{{ scope.row.merchant.is_trader ? '自营' : '非自营' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="broadcast_room_id" label="ID" min-width="60" />
        <el-table-column prop="name" label="直播间名称" min-width="90" />
        <el-table-column
          v-if="tableFrom.status_tag == 1"
          key="1"
          prop="broadcast_room_id"
          label="直播间ID"
          min-width="80"
        />
        <el-table-column prop="anchor_name" label="主播昵称" min-width="90" />
        <el-table-column prop="anchor_wechat" label="主播微信号" min-width="100" />
        <el-table-column prop="start_time" min-width="150" label="直播开始时间" />
        <el-table-column prop="end_time" min-width="150" label="直播计划结束时间" />
        <el-table-column label="直播状态" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.live_status | broadcastStatusFilter }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" min-width="150" label="创建时间" />
        <el-table-column prop="sort" min-width="60" label="排序" />
        <el-table-column label="显示状态" min-width="100">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.is_show"
              :active-value="1"
              :inactive-value="0"
              active-text="显示"
              inactive-text="隐藏"
              @click.native="onchangeIsShow(scope.row)"
            />
          </template>
        </el-table-column>
        <el-table-column  label="审核状态" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.status | liveReviewStatusFilter }}</span>
            <span
              v-if="scope.row.status == -1"
              style="display: block; font-size: 12px;"
            >原因 {{ scope.row.error_msg }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="150" fixed="right">
          <template slot-scope="scope">
            <el-button
              v-if="scope.row.status == 0"
              type="text"
              size="small"
              @click="handleAudit(scope.row.broadcast_room_id)"
            >审核</el-button>
            <el-button
              type="text"
              size="small"
              class="mr10"
              @click="onStudioDetails(scope.row.broadcast_room_id)"
            >详情</el-button>
            <el-button
              type="text"
              size="small"
              class="mr10"
              @click="handleEdit(scope.row.broadcast_room_id)"
            >编辑</el-button>
            <el-button
              v-if="scope.row.status !== 2 || scope.row.live_status === 103 || scope.row.live_status === 107"
              type="text"
              size="small"
              @click="handleDelete(scope.row.broadcast_room_id, scope.$index)"
            >删除</el-button>
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
    <details-from ref="studioDetail" @getList="getList" />
  </div>
</template>

<script>
import {
  broadcastListApi,
  changeReplayApi,
  changeDisplayApi,
  broadcastDeleteApi,
  broadcastAuditApi,
} from "@/api/marketing";
import detailsFrom from "./studioDetail";
import { roterPre } from "@/settings";
export default {
  name: "StudioList",
  components: { detailsFrom },
  data() {
    return {
      Loading: false,
      dialogVisible: false,
      broadcast_room_id: 0,
      roterPre: roterPre,
      listLoading: true,
      tableData: {
        data: [],
        total: 0,
      },
      tableFrom: {
        page: 1,
        limit: 20,
        status_tag: "",
        is_trader: ''
      },
    };
  },
  mounted() {
    this.getList();
  },
  methods: {
    // 删除
    handleDelete(id, idx) {
      this.$modalSureDelete().then(() => {
        broadcastDeleteApi(id)
          .then(({ message }) => {
            this.$message.success(message)
            this.tableData.data.splice(idx, 1)
          })
          .catch(({ message }) => {
            this.$message.error(message)
          })
      })
    },
    // 详情
    onStudioDetails(id) {
      this.broadcast_room_id = id
      this.$refs.studioDetail.dialogVisible = true
      this.$refs.studioDetail.isEdit = false
      this.$refs.studioDetail.getData(id)
    },
    // 编辑
    handleEdit(id) {
      this.broadcast_room_id = id
      this.$refs.studioDetail.dialogVisible = true
      this.$refs.studioDetail.isEdit = true
      this.$refs.studioDetail.getData(id)
    },
    // 列表
    getList(num) {
      this.listLoading = true;
      this.tableFrom.page = num ? num : this.tableFrom.page
      broadcastListApi(this.tableFrom)
        .then((res) => {
          this.tableData.data = res.data.list
          this.tableData.total = res.data.count
          this.listLoading = false;
        })
        .catch((res) => {
          this.listLoading = false
          this.$message.error(res.message)
        })
    },
    pageChange(page) {
      this.tableFrom.page = page;
      this.getList();
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val;
      this.getList();
    },
    // 审核
    handleAudit(id) {
      this.$modalForm(broadcastAuditApi(id)).then(() => this.getList());
    },
    // 修改显示状态
    onchangeIsShow(row) {
      changeDisplayApi(row.broadcast_room_id, { is_show: row.is_show })
        .then(({ message }) => {
          this.$message.success(message);
          this.getList();
        })
        .catch(({ message }) => {
          this.$message.error(message);
        });
    },

  },
};
</script>

<style scoped lang="scss">
.modalbox {
  /deep/.el-dialog {
    min-width: 550px;
  }
}
.selWidth {
  width: 350px !important;
}
.seachTiele {
  line-height: 35px;
}
.fa {
  color: #0a6aa1;
  display: block;
}
.sheng {
  color: #ff0000;
  display: block;
}
</style>
