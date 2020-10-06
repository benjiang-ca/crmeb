<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <el-button size="small" type="primary" @click="onAdd">添加配置</el-button>
      </div>
      <el-table
        v-loading="listLoading"
        :data="tableData.data"
        style="width: 100%"
        size="small"
      >
        <el-table-column
          prop="config_id"
          label="ID"
          min-width="60"
        />
        <el-table-column
          prop="config_name"
          label="配置名称"
          min-width="130"
        />
        <el-table-column
          prop="config_key"
          label="配置key"
          min-width="130"
        />
        <el-table-column
          prop="info"
          label="配置说明"
          min-width="150"
        />
        <el-table-column
          prop="typeName"
          label="类型"
          min-width="130"
        />
        <el-table-column
          prop="user_type"
          label="后台类型"
          min-width="100"
        >
          <template slot-scope="scope">
            <span v-text="scope.row.user_type===0?'总后台配置':'商户后台配置'" />
          </template>
        </el-table-column>
        <el-table-column
          prop="status"
          label="是否显示"
          min-width="100"
        >
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.status"
              :active-value="1"
              :inactive-value="0"
              active-text="显示"
              inactive-text="隐藏"
              @change="onchangeIsShow(scope.row)"
            />
          </template>
        </el-table-column>
        <el-table-column
          prop="create_time"
          label="创建时间"
          min-width="150"
        />
        <el-table-column label="操作" min-width="100" fixed="right">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="onEdit(scope.row.config_id)">编辑</el-button>
            <el-button type="text" size="small" @click="handleDelete(scope.row.config_id, scope.$index)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination
          :page-sizes="[20, 40, 60, 80]"
          :page-size="tableData.limit"
          :current-page="tableData.page"
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
  createConfigSettingTable,
  updateConfigSettingTable,
  configSettingLst,
  changeConfigSettingStatus,
  settingDelApi
} from '@/api/system'
export default {
  name: 'Setting',
  data() {
    return {
      tableData: {
        page: 1,
        limit: 20,
        data: [],
        total: 0
      },
      listLoading: true
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    // 列表
    getList() {
      this.listLoading = true
      configSettingLst(this.tableData.page, this.tableData.limit).then(res => {
        this.tableData.data = res.data.list
        this.tableData.total = res.data.count
        this.listLoading = false
      }).catch(res => {
        this.listLoading = false
        this.$message.error(res.message)
      })
    },
    pageChange(page) {
      this.tableData.page = page
      this.getList()
    },
    handleSizeChange(val) {
      this.tableData.limit = val
      this.getList()
    },
    onchangeIsShow(row) {
      changeConfigSettingStatus(row.config_id, row.status).then(({ message }) => {
        this.$message.success(message)
      }).catch(({ message }) => {
        this.$message.error(message)
      })
    },
    // 添加
    onAdd() {
      this.$modalForm(createConfigSettingTable()).then(() => this.getList())
    },
    // 编辑
    onEdit(id) {
      this.$modalForm(updateConfigSettingTable(id)).then(() => this.getList())
    },
    // 删除
    handleDelete(id, idx) {
      this.$modalSure().then(() => {
        settingDelApi(id).then(({ message }) => {
          this.$message.success(message)
          this.tableData.data.splice(idx, 1)
        }).catch(({ message }) => {
          this.$message.error(message)
        })
      })
    }
  }
}
</script>

<style scoped lang="stylus">

</style>
