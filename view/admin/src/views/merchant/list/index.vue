<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <div class="container">
          <el-form size="small" label-width="100px" :inline="true">
            <el-form-item label="选择时间：" style="display: inline-block">
              <el-radio-group
                v-model="tableFrom.date"
                size="small"
                @change="selectChange(tableFrom.date)"
              >
                <el-radio-button
                  v-for="(itemn,indexn) in fromList.fromTxt"
                  :key="indexn"
                  :label="itemn.val"
                >{{ itemn.text }}</el-radio-button>
              </el-radio-group>
              <el-date-picker
                v-model="timeVal"
                type="daterange"
                placeholder="选择日期"
                format="yyyy/MM/dd"
                value-format="yyyy/MM/dd"
                range-separator="至"
                start-placeholder="开始日期"
                end-placeholder="结束日期"
                @change="onchangeTime"
              />
            </el-form-item>
            <el-form-item label="关键字：" label-width="80px" style="display: inline-block;">
              <el-input
                v-model="tableFrom.keyword"
                placeholder="请输入店铺关键字/店铺名/联系电话"
                class="selWidth"
              >
                <el-button slot="append" icon="el-icon-search" @click="getList(1)" />
              </el-input>
            </el-form-item>
            <el-form-item label="商户类别：">
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
            <el-tabs v-if="headeNum.length > 0" v-model="tableFrom.status" @tab-click="getList(1)">
              <el-tab-pane v-for="(item,index) in headeNum" :key="index" :name="item.type.toString()" :label="item.title +'('+item.count +')' " />
            </el-tabs>

          </el-form>
        </div>
        <el-button size="small" type="primary" @click="onAdd">添加商户</el-button>
      </div>
      <el-table
        v-loading="listLoading"
        :data="tableData.data"
        style="width: 100%"
        size="small"
        highlight-current-row
        class="switchTable"
      >
        <el-table-column prop="mer_id" label="ID" min-width="60" />
        <el-table-column prop="mer_name" label="商户名称" min-width="150" />
        <el-table-column prop="mer_name" label="商户类别" min-width="90">
          <template slot-scope="scope">
            <span class="spBlock">{{ scope.row.is_trader ? '自营' : '非自营' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="real_name" label="商户姓名" min-width="150" />
        <el-table-column prop="mer_phone" label="商户手机号" min-width="100" />
        <el-table-column prop="mer_address" label="商户地址" min-width="200" />
        <el-table-column prop="mark" label="备注" min-width="200" />
        <el-table-column prop="status" label="推荐" min-width="100">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.is_best"
              :active-value="1"
              :inactive-value="0"
              active-text="是"
              inactive-text="否"
              disabled
              @click.native="onchangeIsShow(scope.row)"
            />
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" min-width="150" />
        <el-table-column prop="status" label="开启/关闭" min-width="100">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.status"
              :active-value="1"
              :inactive-value="0"
              active-text="开启"
              inactive-text="关闭"
              disabled
              @click.native="onchangeIsClose(scope.row)"
            />
          </template>
        </el-table-column>
        <el-table-column label="操作" min-width="280" fixed="right" align="center">
          <template slot-scope="scope">
            <router-link
              v-if="tableFrom.status === '1'"
              :to="{path: roterPre+ '/merchant/list/reconciliation/' + scope.row.mer_id + '/1' }"
            >
              <el-button type="text" size="small" class="mr10">对账</el-button>
            </router-link>
            <el-button v-if="tableFrom.status === '1'" type="text" size="small" @click="onLogo(scope.row.mer_id)">登录</el-button>
            <el-button type="text" size="small" @click="onEdit(scope.row.mer_id)">编辑</el-button>
            <el-button v-if="tableFrom.status === '1'" type="text" size="small" @click="onPassword(scope.row.mer_id)">修改管理员密码</el-button>
            <el-button
              v-if="tableFrom.status === '0'"
              type="text"
              size="small"
              @click="handleDelete(scope.row.mer_id, scope.$index)"
            >删除</el-button>
            <el-button
              v-if="tableFrom.status === '1'"
              type="text"
              size="small"
              @click="handleTimes(scope.row.mer_id)"
            >设置第三方平台商品复制次数</el-button>
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
  merchantListApi,
  merchantCreateApi,
  merchantUpdateApi,
  merchantDeleteApi,
  merchantStatuseApi,
  merchantPasswordApi,
  merchantLoginApi,
  changeCopyApi,
  merchantCountApi,
  merchantIsCloseApi
} from '@/api/merchant'
import { fromList } from '@/libs/constants.js'
import { roterPre } from '@/settings'
import SettingMer from '@/libs/settingMer'
import Cookies from 'js-cookie'
export default {
  name: 'MerchantList',
  data() {
    return {
      fromList: fromList,
      roterPre: roterPre,
      isChecked: false,
      listLoading: true,
      headeNum: [
        {
        count: 270,
        type: '1',
        title: "正常开启的商户"
      },
        {
          count: 270,
          type: '0',
          title: "已关闭商户"
        }
      ],
      tableData: {
        data: [],
        total: 0
      },
      tableFrom: {
        page: 1,
        limit: 20,
        date: '',
        status: '1',
        keyword: '',
        is_trader: ''
      },
      autoUpdate: true,
      timeVal: []
    }
  },
  mounted() {
    this.getHeadNum()
    this.getList('')
  },
  methods: {
    onLogo(id) {
      merchantLoginApi(id)
        .then((res) => {
          Cookies.set('merchantToken', res.data.token)
          window.open(SettingMer.httpUrl + res.data.url)
        })
        .catch((res) => {
          this.$message.error(res.message)
        })
    },
    // 选择时间
    selectChange(tab) {
      this.tableFrom.date = tab
      this.timeVal = []
      this.tableFrom.page = 1;
      this.getList('')
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e
      this.tableFrom.date = this.timeVal ? this.timeVal.join('-') : ''
      this.tableFrom.page = 1;
      this.getList('')
    },
    // 获取开启商户数
    getHeadNum(){
      merchantCountApi()
        .then((res) => {
          this.headeNum[0]['count'] = res.data.valid;
          this.headeNum[1]['count'] = res.data.invalid;
        })
        .catch((res) => {

        })
    },
    // 列表
    getList(num) {
      this.listLoading = true
      this.tableFrom.page = num ? num : this.tableFrom.page;
      merchantListApi(this.tableFrom)
        .then((res) => {
          this.tableData.data = res.data.list
          this.tableData.total = res.data.count
          this.listLoading = false
        })
        .catch((res) => {
          this.listLoading = false
          this.$message.error(res.message)
        })
    },
    pageChange(page) {
      this.tableFrom.page = page
      this.getList('')
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val
      this.getList(1)
    },
    // 修改状态
    onchangeIsShow(row) {
      const title = row.is_best === 0 ? '是否开启推荐商户' : '是否关闭推荐商户'
      this.$modalSure(title).then(() => {
        merchantStatuseApi(row.mer_id, row.is_best === 1 ? 0 : 1)
          .then(({ message }) => {
            this.$message.success(message)
            this.getList('')
          })
          .catch(({ message }) => {
            this.$message.error(message)
          })
      })
    },
    // 开启关闭
    onchangeIsClose(row) {
      merchantIsCloseApi(row.mer_id, row.status === 1 ? 0 : 1)
          .then(({ message }) => {
            this.$message.success(message)
            this.getList('');
          })
          .catch(({ message }) => {
            this.$message.error(message)
          })

    },
    // 添加
    onAdd() {
      this.$modalForm(merchantCreateApi()).then(() => this.getList(''))
    },
    // 编辑
    onEdit(id) {
      this.$modalForm(merchantUpdateApi(id)).then(() => this.getList(''))
    },
    // 删除
    handleDelete(id) {
      this.$modalSure('该商户下有相关数据信息，删除后不可恢复，您是否确定删除').then(() => {
        merchantDeleteApi(id)
          .then(({ message }) => {
            this.$message.success(message)
            this.getList('')
            this.getHeadNum()
          })
          .catch(({ message }) => {
            this.$message.error(message)
          })
      })
    },
    // 设置复制次数
    handleTimes(id) {
      this.$modalForm(changeCopyApi(id)).then(() => this.getList(''))
    },

    // 修改密码表单
    onPassword(id) {
      this.$modalForm(merchantPasswordApi(id))
    }
  }
}
</script>

<style scoped lang="scss">
</style>
