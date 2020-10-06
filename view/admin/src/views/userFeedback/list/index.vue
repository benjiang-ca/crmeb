<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <el-form inline size="small">
          <el-form-item label="关键字：">
            <el-input v-model="tableFrom.keyword" placeholder="请输入关键字" class="selWidth" size="small">
              <el-button slot="append" icon="el-icon-search" size="small" @click="getList" />
            </el-input>
          </el-form-item>
        </el-form>
      </div>
      <el-table
        v-loading="listLoading"
        :data="tableData.data"
        style="width: 100%"
        size="small"
      >
        <el-table-column
          prop="feedback_id"
          label="ID"
          min-width="60"
        />
        <el-table-column
          prop="realname"
          label="用户姓名"
          min-width="150"
        />
        <el-table-column
          prop="contact"
          label="联系方式"
          min-width="150"
        />
        <el-table-column
          prop="content"
          label="问题描述"
          min-width="150"
        />
        <el-table-column label="描述图片" min-width="80">
          <template slot-scope="scope">
            <div class="demo-image__preview" v-for="item in scope.row.images">
              <el-image
                style="width: 36px; height: 36px"
                :src="item"
                :preview-src-list="[item]"
              />
            </div>
          </template>
        </el-table-column>
        <el-table-column
          label="分类"
          min-width="150"
        >
          <template slot-scope="scope">
            <span>{{ scope.row.type ? scope.row.type.cate_name : '' }}</span>
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
          label="反馈时间"
          min-width="150"
        />
        <el-table-column label="操作" min-width="100" fixed="right">
          <template slot-scope="scope">
            <!--<el-button type="text" size="small" @click="onEdit(scope.row.feedback_id)">备注</el-button>-->
            <el-button type="text" size="small" @click="handleDelete(scope.row.feedback_id, scope.$index)">删除</el-button>
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
import { feedbackListApi, feedbackReplyApi, feedbackDeleteApi } from '@/api/userFeedback'
export default {
  name: 'Classify',
  data() {
    return {
      tableData: {
        data: [],
        total: 0
      },
      tableFrom: {
        page: 1,
        limit: 20,
        keyword: ''
        // type: '',
        // realname: '',
        // status: ''
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
      feedbackListApi(this.tableFrom).then(res => {
        this.tableData.data = res.data.list
        this.tableData.total = res.data.count
        this.listLoading = false
      }).catch(res => {
        this.listLoading = false
        this.$message.error(res.message)
      })
    },
    pageChange(page) {
      this.tableFrom.page = page
      this.getList()
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val
      this.getList()
    },
    onchangeIsShow(row) {
      changeConfigClassifyStatus(row.config_classify_id, row.status).then(({ message }) => {
        this.$message.success(message)
      }).catch(({ message }) => {
        this.$message.error(message)
      })
    },
    // 编辑
    onEdit(id) {
      this.$prompt('备注内容', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        inputErrorMessage: '请输入备注内容',
        inputType: 'textarea',
        inputPlaceholder: '请输入回复内容',
        inputValidator: (value) => {
          if (!value) {
            return '输入不能为空'
          }
        }
      }).then(({ value }) => {
        feedbackReplyApi(id).then(res => {
          this.$message({
            type: 'success',
            message: '备注成功'
          })
          this.getList()
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '取消输入'
        })
      })
    },
    // 删除
    handleDelete(id, idx) {
      this.$modalSure().then(() => {
        feedbackDeleteApi(id).then(({ message }) => {
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
