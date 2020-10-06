<template>
  <div class="importedGoods">
    <el-table
      ref="table"
      v-loading="listLoading"
      :data="tableData.data"
      style="width: 100%"
      size="samll"
      highlight-current-row
    >
      <el-table-column prop="product_id" label="ID" min-width="50" />
      <el-table-column label="商品图" min-width="80">
        <template slot-scope="scope">
          <el-image
              style="width: 36px; height: 36px"
              :src="scope.row.cover_img"
            />
        </template>
      </el-table-column>
      <el-table-column prop="name" label="商品名称" min-width="200" />
      <el-table-column label="库存" min-width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.product.stock }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="price" label="直播价" min-width="80" />
      <el-table-column prop="pay_num" label="销售数量" min-width="80" />
      <el-table-column prop="pay_price" label="销售金额" min-width="80" />
    </el-table>
    <div class="block mb20">
      <el-pagination
        :page-sizes="[3, 6, 9]"
        :page-size="tableFrom.limit"
        :current-page="tableFrom.page"
        layout="total, sizes, prev, pager, next, jumper"
        :total="tableData.total"
        @size-change="handleSizeChange"
        @current-change="pageChange"
      />
    </div>
  </div>
</template>

<script>
import { studioProList } from "@/api/marketing";
export default {
  name: "GoodsList",
  data() {
    return {
      listLoading: true,
      multipleSelection: [],
      tableData: {
        data: [],
        total: 0,
      },
      tableFrom: {
        page: 1,
        limit: 3,
        keyword: "",
      },
    };
  },
  props: {
    broadcast_room_id:{
      type: Number
    }
  },
  watch: {
    broadcast_room_id: {
      deep: true,
      handler(val) {
        this.getList(val)
      }
    }

  },
  mounted() {
    this.getList(this.broadcast_room_id);
  },
  methods: {
    // 列表
    getList(id) {
      this.listLoading = true;
      studioProList(id,this.tableFrom)
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
      this.getList(this.broadcast_room_id);
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val;
      this.getList(this.broadcast_room_id);
    },
  },
};
</script>

<style scoped lang="scss">

</style>
