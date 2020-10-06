<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <div class="container">
          <el-form size="small" label-width="100px">
            <el-form-item label="状态：">
              <el-radio-group v-model="tableForm.status_tag" type="button" @change="getList">
                <el-radio-button label>全部</el-radio-button>
                <el-radio-button label="0">待审核</el-radio-button>
                <el-radio-button label="1">已审核</el-radio-button>
                <el-radio-button label="-1">审核失败</el-radio-button>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="关键字：" class="width100">
              <el-input
                v-model="tableForm.keyword"
                placeholder="请输入直播间名称/ID/主播昵称/微信号"
                class="selWidth"
                size="small"
              >
                <el-button slot="append" icon="el-icon-search" @click="getList" />
              </el-input>
            </el-form-item>
          </el-form>
          <router-link :to=" { path:`${roterPre}` + '/marketing/studio/creatProduct' } ">
            <el-button size="small" type="primary">创建直播商品</el-button>
          </router-link>
        </div>
      </div>
      <el-table
        v-loading="listLoading"
        :data="tableData.data"
        style="width: 100%"
        size="small"
        highlight-current-row
      >
        <el-table-column label="序号" min-width="90">
          <template scope="scope">
            <span>{{ scope.$index+(tableForm.page - 1) * tableForm.limit + 1 }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="product_id" label="商品ID" min-width="120" />
        <el-table-column prop="name" label="商品名称" min-width="90" />
        <el-table-column label="原价" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.product.price }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="price" label="直播价" min-width="150" />
        <el-table-column label="库存" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.product.stock }}</span>
          </template>
        </el-table-column>
        <el-table-column v-if="tableForm.status_tag != 1" key="3" label="审核状态" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.status | liveReviewStatusFilter }}</span>
          </template>
        </el-table-column>
        <el-table-column label="是否上架" min-width="100">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.is_mer_show"
              :active-value="1"
              :inactive-value="0"
              active-text="上架"
              inactive-text="下架"
              @click.native="onchangeIsShow(scope.row)"
            />
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="150" fixed="right">
          <template slot-scope="scope">
            <router-link
              v-if="scope.row.status == 0"
              :to="{path: roterPre + '/marketing/seckill/createGoods/' + scope.row.product_id}"
            > <el-button type="text" size="small" class="mr10">编辑</el-button></router-link>
            <router-link
              :to=" { path:`${roterPre}` + '/marketing/seckill/creatCoupon/' + scope.row.broadcast_goods_id } "
            >
              <el-button type="text" size="small" class="mr10">详情</el-button>
            </router-link>
            <el-button
              type="text"
              size="small"
              @click="handleDelete(scope.row.broadcast_goods_id, scope.$index)"
            >删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination
          :page-sizes="[20, 40, 60, 80]"
          :page-size="tableForm.limit"
          :current-page="tableForm.page"
          layout="total, sizes, prev, pager, next, jumper"
          :total="tableData.total"
          @size-change="handleSizeChange"
          @current-change="pageChange"
        />
      </div>
    </el-card>
  </div>
</template>

<script>
import { broadcastProListApi, changeProDisplayApi } from "@/api/marketing";
import { roterPre } from "@/settings";
export default {
  name: "StudioList",
  data() {
    return {
      Loading: false,
      roterPre: roterPre,
      listLoading: true,
      tableData: {
        data: [],
        total: 0,
      },
      tableForm: {
        page: 1,
        limit: 20,
        status_tag: "",
        keyword: "",
      },
      liveRoomStatus: "",
    };
  },
  mounted() {
    this.getList();
  },
  methods: {
    // 编辑
    handleEdit() {},
    // 删除
    handleDelete(id, idx) {
      this.$modalSure().then(() => {
        couponDeleteApi(id)
          .then(({ message }) => {
            this.$message.success(message);
            this.tableData.data.splice(idx, 1);
          })
          .catch(({ message }) => {
            this.$message.error(message);
          });
      });
    },
    handleSizeChangeIssue(val) {
      this.tableFormIssue.limit = val;
      this.getIssueList();
    },
    // 列表
    getList() {
      this.listLoading = true;
      console.log(this.tableForm);
      broadcastProListApi(this.tableForm)
        .then((res) => {
          this.tableData.data = res.data.list;
          this.tableData.total = res.data.count;
          this.listLoading = false;
        })
        .catch((res) => {
          this.listLoading = false;
          this.$message.error(res.message);
        });
    },
    pageChange(page) {
      this.tableForm.page = page;
      this.getList();
    },
    handleSizeChange(val) {
      this.tableForm.limit = val;
      this.getList();
    },
    // 修改状态
    onchangeIsShow(row) {
      changeProDisplayApi(row.broadcast_goods_id, row.is_mer_show)
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
  width: 400px;
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
