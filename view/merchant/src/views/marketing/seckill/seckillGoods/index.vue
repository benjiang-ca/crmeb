<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <el-tabs v-model="tableFrom.type" @tab-click="getList">
          <el-tab-pane
            v-for="(item,index) in headeNum"
            :key="index"
            :name="item.type.toString()"
            :label="item.name +'('+item.count +')' "
          />
        </el-tabs>
        <div class="container">
          <el-form size="small" label-width="120px" :inline="true">
            <el-form-item label="秒杀状态：">
              <el-select
                v-model="tableFrom.seckill_status"
                placeholder="请选择"
                class="filter-item selWidth mr20"
                clearable
                @change="getList"
              >
                <el-option
                  v-for="item in seckillStatusList"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                />
              </el-select>
            </el-form-item>
            <el-form-item label="关键字搜索：">
              <el-input v-model="tableFrom.keyword" placeholder="请输入商品名称，关键字，产品编号" class="selWidth">
                <el-button slot="append" icon="el-icon-search" @click="getList" />
              </el-input>
            </el-form-item>
          </el-form>
        </div>
        <router-link :to=" { path:`${roterPre}` + '/marketing/seckill/createGoods' } ">
          <el-button size="small" type="primary">
            <i class="add">+</i> 添加秒杀商品
          </el-button>
        </router-link>
      </div>
      <el-table v-loading="listLoading" :data="tableData.data" style="width: 100%" size="mini">
        <el-table-column type="expand">
          <template slot-scope="props">
            <el-form label-position="left" inline class="demo-table-expand demo-table-expand1">
              <el-form-item label="平台分类：">
                <span>{{ props.row.storeCategory?props.row.storeCategory.cate_name:'-' }}</span>
              </el-form-item>
              <el-form-item label="商品分类：">
                <template v-if="props.row.merCateId.length">
                  <span
                    v-for="(item, index) in props.row.merCateId"
                    :key="index"
                    class="mr10"
                  >{{ item.category.cate_name }}</span>
                </template>
                <span v-else>-</span>
              </el-form-item>
              <el-form-item label="品牌：">
                <span class="mr10">{{ props.row.brand?props.row.brand.brand_name:'-' }}</span>
              </el-form-item>
              <el-form-item label="市场价格：">
                <span>{{ props.row.ot_price | filterEmpty }}</span>
              </el-form-item>
              <el-form-item label="成本价：">
                <span>{{ props.row.cost | filterEmpty }}</span>
              </el-form-item>
              <el-form-item label="收藏：">
                <span>{{ props.row.care_count | filterEmpty }}</span>
              </el-form-item>
              <el-form-item label="未通过原因：" v-if="tableFrom.type === '7'" key="1">
                <span>{{ props.row.care_count | filterEmpty }}</span>
              </el-form-item>
            </el-form>
          </template>
        </el-table-column>
        <el-table-column prop="product_id" label="ID" min-width="50" />
        <el-table-column label="商品图" min-width="80">
          <template slot-scope="scope">
            <div class="demo-image__preview">
              <el-image :src="scope.row.image" :preview-src-list="[scope.row.image]" />
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="store_name" label="活动标题" min-width="120" />
        <el-table-column prop="store_info" label="商品简介" min-width="120" />
        <el-table-column prop="price" label="秒杀价" min-width="90" />
        <el-table-column label="限量剩余" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.stock - scope.row.sales }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="stock" label="秒杀状态" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.seckill_status | seckillStatusFilter }}</span>
          </template>
        </el-table-column>
        <el-table-column label="秒杀活动日期" min-width="160">
          <template slot-scope="scope">
            <div>开始日期：{{ scope.row.seckillActive && scope.row.seckillActive.start_day ? scope.row.seckillActive.start_day.slice(0,10) : "" }}</div>
            <div>结束日期：{{ scope.row.seckillActive && scope.row.seckillActive.end_day ? scope.row.seckillActive.end_day.slice(0,10) : "" }}</div>
          </template>
        </el-table-column>
        <el-table-column label="秒杀活动时间" min-width="130">
          <template slot-scope="scope">
            <div>开始时间：{{ scope.row.seckillActive && scope.row.seckillActive.start_time ? scope.row.seckillActive.start_time+':00' : "" }}</div>
            <div>结束时间：{{ scope.row.seckillActive && scope.row.seckillActive.end_time ? scope.row.seckillActive.end_time+':00' : "" }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" min-width="80">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.is_show"
              :active-value="1"
              :inactive-value="0"
              active-text="上架"
              inactive-text="下架"
              @change="onchangeIsShow(scope.row)"
            />
          </template>
        </el-table-column>
        <el-table-column label="审核状态" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.status | seckillReviewStatusFilter }}</span>
            <span v-if="scope.row.status == -1 || scope.row.status == -2" style="font-size: 12px;">
              <br />
              原因：{{ scope.row.refusal }}
            </span>
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="150" fixed="right">
          <template slot-scope="scope">
            <router-link
              :to="{path: roterPre + '/marketing/seckill/createGoods/' + scope.row.product_id}"
            >
              <el-button type="text" size="small" class="mr10">编辑</el-button>
            </router-link>
            <el-button
              v-if="tableFrom.type === '5'"
              type="text"
              size="small"
              @click="handleRestore(scope.row.product_id)"
            >恢复商品</el-button>
            <el-button
              v-if="tableFrom.type!== '1' && tableFrom.type!== '3' && tableFrom.type !=='4' "
              type="text"
              size="small"
              @click="handleDelete(scope.row.product_id, scope.$index)"
            >{{ tableFrom.type === '5' ? '删除' : '加入回收站' }}</el-button>
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
  </div>
</template>
<script>
import {
  seckillProductLstApi,
  spikeProductDeleteApi,
  categorySelectApi,
  spikelstFilterApi,
  spikeStatusApi,
  categoryListApi,
  spikeDestoryApi,
  spikeRestoreApi,
} from "@/api/product";
import { roterPre } from "@/settings";
export default {
  name: "ProductList",
  data() {
    return {
      props: {
        emitPath: false,
      },
      roterPre: roterPre,
      headeNum: [],
      listLoading: true,
      tableData: {
        data: [],
        total: 0,
      },
      seckillStatusList: [
        { label: "正在进行", value: 2 },
        { label: "活动已结束", value: 3 },
        { label: "活动未开始", value: 1 },
      ],
      tableFrom: {
        page: 1,
        limit: 20,
        mer_cate_id: "",
        cate_id: "",
        keyword: "",
        type: "1",
        seckill_status: "",
      },
      categoryList: [], // 平台
      merCateList: [], // 商户分类筛选
      modals: false,
    };
  },
  mounted() {
    this.getLstFilterApi();
    this.getCategorySelect();
    this.getCategoryList();
    this.getList();
  },
  methods: {
    // 添加淘宝商品成功
    onClose() {
      this.modals = false;
    },

    handleRestore(id) {
      this.$modalSure("恢复商品").then(() => {
        spikeRestoreApi(id)
          .then((res) => {
            this.$message.success(res.message);
            this.getLstFilterApi();
            this.getList();
          })
          .catch((res) => {
            this.$message.error(res.message);
          });
      });
    },
    // 商户分类；
    getCategorySelect() {
      categorySelectApi()
        .then((res) => {
          this.merCateList = res.data;
        })
        .catch((res) => {
          this.$message.error(res.message);
        });
    },
    // 平台分类；
    getCategoryList() {
      categoryListApi()
        .then((res) => {
          this.categoryList = res.data;
        })
        .catch((res) => {
          this.$message.error(res.message);
        });
    },
    // 列表表头；
    getLstFilterApi() {
      spikelstFilterApi()
        .then((res) => {
          this.headeNum = res.data;
        })
        .catch((res) => {
          this.$message.error(res.message);
        });
    },
    // 列表
    getList() {
      this.listLoading = true;
      seckillProductLstApi(this.tableFrom)
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
      this.tableFrom.page = page;
      this.getList();
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val;
      this.getList();
    },
    // 删除
    handleDelete(id, idx) {
      this.$modalSure(this.tableFrom.type !== "5" ? "加入回收站" : "").then(
        () => {
          this.tableFrom.type === "5"
            ? spikeDestoryApi(id)
                .then(({ message }) => {
                  this.$message.success(message);
                  this.getList();
                  this.getLstFilterApi();
                })
                .catch(({ message }) => {
                  this.$message.error(message);
                })
            : spikeProductDeleteApi(id)
                .then(({ message }) => {
                  this.$message.success(message);
                  this.getList();
                  this.getLstFilterApi();
                })
                .catch(({ message }) => {
                  this.$message.error(message);
                });
        }
      );
    },
    onchangeIsShow(row) {
      spikeStatusApi(row.product_id, row.is_show)
        .then(({ message }) => {
          this.$message.success(message);
          this.getList();
          this.getLstFilterApi();
        })
        .catch(({ message }) => {
          this.$message.error(message);
        });
    },
  },
};
</script>

<style scoped lang="scss">
.add {
  font-style: normal;
  position: relative;
  top: -1.2px;
}
.demo-table-expand {
  font-size: 0;
}
.demo-table-expand1 {
  /deep/ label {
    width: 77px !important;
    color: #99a9bf;
  }
}
.demo-table-expand .el-form-item {
  margin-right: 0;
  margin-bottom: 0;
  width: 33.33%;
}
.selWidth {
  width: 350px !important;
}
.seachTiele {
  line-height: 35px;
}
</style>
