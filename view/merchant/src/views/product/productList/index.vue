<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <el-tabs v-model="tableFrom.type" @tab-click="getList(1)">
          <el-tab-pane
            v-for="(item,index) in headeNum"
            :key="index"
            :name="item.type.toString()"
            :label="item.name +'('+item.count +')' "
          />
        </el-tabs>
        <div class="container">
          <el-form size="small" label-width="120px" :inline="true">
            <el-form-item label="平台商品分类：">
              <el-cascader
                class="selWidth"
                v-model="tableFrom.cate_id"
                :options="categoryList"
                :props="props"
                clearable
                @change="getList(1)"
              ></el-cascader>
            </el-form-item>
            <el-form-item label="商户商品分类：">
              <el-select
                v-model="tableFrom.mer_cate_id"
                placeholder="请选择"
                class="filter-item selWidth mr20"
                clearable
                @change="getList(1)"
              >
                <el-option
                  v-for="item in merCateList"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                />
              </el-select>
            </el-form-item>
            <el-form-item label="是否为礼包：">
              <el-select
                v-model="tableFrom.is_gift_bag	"
                placeholder="请选择"
                class="selWidth"
                clearable
                @change="getList(1)"
              >
                <el-option label="是" value="1" />
                <el-option label="否" value="0" />
              </el-select>
            </el-form-item>
            <el-form-item label="关键字搜索：">
              <el-input v-model="tableFrom.keyword" placeholder="请输入商品名称，关键字" class="selWidth">
                <el-button slot="append" icon="el-icon-search" @click="getList(1)" />
              </el-input>
            </el-form-item>
          </el-form>
        </div>
        <router-link :to=" { path:`${roterPre}` + '/product/list/addProduct' } ">
          <el-button size="small" type="primary">添加商品</el-button>
        </router-link>
        <el-button size="small" type="success" @click="onCopy">复制淘宝、天猫、京东、拼多多、苏宁</el-button>
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
                <span>{{ props.row.refusal }}</span>
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
        <el-table-column prop="store_name" label="商品名称" min-width="200" />
        <el-table-column prop="price" label="商品售价" min-width="90" />
        <el-table-column prop="sales" label="销量" min-width="90" />
        <el-table-column prop="stock" label="库存" min-width="90" />
        <el-table-column prop="sort" label="排序" min-width="70" />
        <el-table-column prop="status" label="状态" min-width="150" v-if="Number(tableFrom.type) < 5" key="1">
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
        <el-table-column prop="create_time" label="创建时间" min-width="150" />
        <el-table-column label="操作" min-width="150" fixed="right">
          <template slot-scope="scope">
            <router-link
              :to="{path: roterPre + '/product/list/addProduct/' + scope.row.product_id}"
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
    <!-- 生成淘宝京东表单-->
    <tao-bao ref="taoBao" @getSuccess="getSuccess"/>
  </div>
</template>

<script>
import {
  productLstApi,
  productDeleteApi,
  categorySelectApi,
  lstFilterApi,
  statusApi,
  categoryListApi,
  destoryApi,
  restoreApi,
} from "@/api/product";
import { roterPre } from "@/settings";
import taoBao from "./taoBao";
export default {
  name: "ProductList",
  components: { taoBao },
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
      tableFrom: {
        page: 1,
        limit: 20,
        mer_cate_id: "",
        cate_id: "",
        keyword: "",
        type: "1",
        is_gift_bag: "",
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
    this.getList(1);
  },
  methods: {
    getSuccess() {
      this.getLstFilterApi()
      this.getList(1);
    },
    // 添加淘宝商品成功
    onClose() {
      this.modals = false;
    },
    // 复制淘宝
    onCopy() {
      this.$refs.taoBao.modals = true;
      this.$refs.taoBao.soure_link = '';
      this.$refs.taoBao.formValidate = {};
      this.$refs.taoBao.isData = false;
    },
    handleRestore(id) {
      this.$modalSure("恢复商品").then(() => {
        restoreApi(id)
          .then((res) => {
            this.$message.success(res.message);
            this.getLstFilterApi();
            this.getList('');
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
      lstFilterApi()
        .then((res) => {
          this.headeNum = res.data;
        })
        .catch((res) => {
          this.$message.error(res.message);
        });
    },
    // 列表
    getList(num) {
      this.listLoading = true;
      this.tableFrom.page = num ? num : this.tableFrom.page;
      productLstApi(this.tableFrom)
        .then((res) => {
          this.tableData.data = res.data.list;
          this.tableData.total = res.data.count;
          this.listLoading = false;
        })
        .catch((res) => {
          this.listLoading = false;
          this.$message.error(res.message);
        });
      this.getLstFilterApi();
    },
    pageChange(page) {
      this.tableFrom.page = page;
      this.getList('');
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val;
      this.getList('');
    },
    // 删除
    handleDelete(id, idx) {
      this.$modalSure(this.tableFrom.type !== "5" ? "加入回收站" : "").then(
        () => {
          this.tableFrom.type === "5"
            ? destoryApi(id)
                .then(({ message }) => {
                  this.$message.success(message);
                  this.getList('');
                  this.getLstFilterApi();
                })
                .catch(({ message }) => {
                  this.$message.error(message);
                })
            : productDeleteApi(id)
                .then(({ message }) => {
                  this.$message.success(message);
                  this.getList('');
                  this.getLstFilterApi();
                })
                .catch(({ message }) => {
                  this.$message.error(message);
                });
        }
      );
    },
    onchangeIsShow(row) {
      statusApi(row.product_id, row.is_show)
        .then(({ message }) => {
          this.$message.success(message);
          this.getList('');
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
