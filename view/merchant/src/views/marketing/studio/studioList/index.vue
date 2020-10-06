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
                            <el-radio-button label="1">审核已通过</el-radio-button>
                            <el-radio-button label="-1">审核未通过</el-radio-button>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="关键字：" class="width100">
                        <el-input v-model="tableForm.keyword" placeholder="请输入直播间名称/ID/主播昵称/微信号" class="selWidth" size="small">
                            <el-button slot="append" icon="el-icon-search" @click="getList" />
                        </el-input>
                    </el-form-item>
                </el-form>
                <router-link :to=" { path:`${roterPre}` + '/marketing/studio/creatStudio' } ">
                    <el-button size="small" type="primary">创建直播间</el-button>
                </router-link>
            </div>
        </div>
        <el-table v-loading="listLoading" :data="tableData.data" style="width: 100%" size="small" highlight-current-row>
            <el-table-column label="序号" min-width="90">
                <template scope="scope">
                    <span>{{ scope.$index+(tableForm.page - 1) * tableForm.limit + 1 }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="name" label="直播间名称" min-width="120" />
            <el-table-column prop="broadcast_room_id" label="直播间ID" min-width="90" />
            <el-table-column prop="anchor_name" label="主播昵称" min-width="90" />
            <el-table-column prop="anchor_wechat" label="主播微信号" min-width="100" />
            <el-table-column prop="phone" label="主播手机号" min-width="150" />
            <el-table-column prop="start_time" min-width="150" label="直播开始时间" />
            <el-table-column prop="end_time" min-width="150" label="直播计划结束时间" />
            <el-table-column label="直播状态" min-width="100">
                <template slot-scope="scope">
                    <span>{{ scope.row.live_status | broadcastStatusFilter }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="create_time" min-width="150" label="创建时间" />
            <el-table-column v-if="tableForm.status_tag !== -1" key="5" label="显示状态" min-width="100">
                <template slot-scope="scope">
                    <el-switch v-model="scope.row.is_mer_show" :active-value="1" :inactive-value="0" active-text="显示" inactive-text="隐藏" @click.native="onchangeIsShow(scope.row)" />
                </template>
            </el-table-column>
            <el-table-column  label="审核状态" min-width="100">
                <template slot-scope="scope">
                    <span>{{ scope.row.status | liveReviewStatusFilter }}</span>
                    <span v-if="scope.row.status === -1" style="display: block; font-size: 12px;">原因 {{ scope.row.error_msg }}</span>
                </template>
            </el-table-column>
            <el-table-column label="直播商品" min-width="100">
                <template slot-scope="scope">
                    <el-button v-if="scope.row.status === 2" key="4" type="text" size="small" @click="handleImport(scope.row)">导入</el-button>
                </template>
            </el-table-column>
            <el-table-column label="操作" min-width="150" fixed="right">
                <template slot-scope="scope">
                  <el-button type="text" size="small" class="mr10" @click="onStudioDetails(scope.row.broadcast_room_id)">详情</el-button>
                  <el-button
                    v-if="scope.row.status !== 2 || scope.row.live_status === 103 || scope.row.status !== 1"
                    type="text"
                    size="small"
                    @click="handleDelete(scope.row.broadcast_room_id, scope.$index)"
                  >删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="block">
            <el-pagination :page-sizes="[20, 40, 60, 80]" :page-size="tableForm.limit" :current-page="tableForm.page" layout="total, sizes, prev, pager, next, jumper" :total="tableData.total" @size-change="handleSizeChange" @current-change="pageChange" />
        </div>
    </el-card>
    <!--详情-->
    <details-from ref="studioDetail" />
    <!--导入直播商品-->
    <import-goods ref="uploadGoods"/>
</div>
</template>

<script>
  import {
    broadcastListApi,
    changeStudioRoomDisplayApi,
    broadcastDeleteApi
  } from '@/api/marketing'
import {
    roterPre
} from '@/settings'
import detailsFrom from './studioDetail'
import importGoods from '@/components/importGoods/index'
export default {
    name: 'StudioList',
    components: {
        detailsFrom,
        importGoods
    },
    data() {
        return {
            Loading: false,
            roterPre: roterPre,
            dialogVisible: false,
            importVisible: false,
            listLoading: true,
            tableData: {
                data: [],
                total: 0
            },
            tableForm: {
                page: 1,
                limit: 20,
                status_tag: '',
                keyword: ''
            },
            liveRoomStatus: ''
        }
    },
    mounted() {
        this.getList()
    },
    methods: {
        // 详情
        onStudioDetails(id) {
            this.broadcast_room_id = id
            this.$refs.studioDetail.dialogVisible = true
            this.$refs.studioDetail.getData(id)
        },
        // 导入
        handleImport(row) {
            this.$refs.uploadGoods.importVisible = true
            this.$refs.uploadGoods.broadcast_id = row.broadcast_room_id
            this.$refs.uploadGoods.broadcast_room = row.name
            this.$refs.uploadGoods.image = ''
            localStorage.setItem('liveRoom_id', row.broadcast_room_id)
        },
      // 删除
      handleDelete(id, idx) {
        this.$modalSureDelete().then(
          () => {
            broadcastDeleteApi(id)
              .then(({ message }) => {
                this.$message.success(message);
                this.tableData.data.splice(idx, 1)
              })
              .catch(({ message }) => {
                this.$message.error(message);
              });
          }
        );
      },

        // 列表
        getList() {
            this.listLoading = true
            console.log(this.tableForm)
            broadcastListApi(this.tableForm)
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
            this.tableForm.page = page
            this.getList()
        },
        handleSizeChange(val) {
            this.tableForm.limit = val
            this.getList()
        },
        // 修改状态
        onchangeIsShow(row) {
            changeStudioRoomDisplayApi(row.broadcast_room_id, {
                    is_show: row.is_mer_show
                })
                .then(({
                    message
                }) => {
                    this.$message.success(message)
                    this.getList()
                })
                .catch(({
                    message
                }) => {
                    this.$message.error(message)
                })
        }
    }
}
</script>

<style lang="scss" scoped>
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
