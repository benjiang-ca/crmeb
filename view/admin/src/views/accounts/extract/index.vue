<template>
<div class="divBox">
    <el-card class="box-card">
        <div slot="header" class="clearfix">
            <div class="container">
                <el-form size="small" label-width="100px">
                    <el-form-item label="时间选择：" class="width100">
                        <el-radio-group v-model="tableFrom.date" type="button" class="mr20" size="small" @change="selectChange(tableFrom.date)">
                            <el-radio-button v-for="(item,i) in fromList.fromTxt" :key="i" :label="item.val">{{ item.text }}</el-radio-button>
                        </el-radio-group>
                        <el-date-picker v-model="timeVal" value-format="yyyy/MM/dd" format="yyyy/MM/dd" size="small" type="daterange" placement="bottom-end" placeholder="自定义时间" style="width: 250px;" @change="onchangeTime" />
                    </el-form-item>
                    <el-form-item label="提现状态：">
                        <el-radio-group v-model="tableFrom.status" type="button" size="small" @change="getList(1)">
                            <el-radio-button label="">全部</el-radio-button>
                            <el-radio-button label="0">审核中</el-radio-button>
                            <el-radio-button label="1">已提现</el-radio-button>
                            <el-radio-button label="-1">已拒绝</el-radio-button>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="提现方式：">
                        <el-radio-group v-model="tableFrom.extract_type" type="button" size="small" @change="getList(1)">
                            <el-radio-button label="">全部</el-radio-button>
                            <el-radio-button label="0">银行卡</el-radio-button>
                            <el-radio-button label="2">支付宝</el-radio-button>
                            <el-radio-button label="1">微信</el-radio-button>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="关键字：" class="width100">
                        <el-input v-model="tableFrom.keyword" placeholder="姓名/支付宝账号/银行卡号" class="selWidth" size="small">
                            <el-button slot="append" icon="el-icon-search" size="small" @click="getList(1)" />
                        </el-input>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <el-table v-loading="listLoading" :data="tableData.data" style="width: 100%" size="mini" class="table" highlight-current-row>
            <el-table-column prop="extract_id" label="ID" width="60" />
            <el-table-column label="二维码" min-width="80">
                <template slot-scope="scope">
                    <div class="demo-image__preview">
                        <el-image :src="scope.row.extract_pic" :preview-src-list="[scope.row.extract_pic]" />
                    </div>
                </template>
            </el-table-column>
            <el-table-column prop="real_name" label="用户信息" min-width="150" />
            <el-table-column prop="extract_price" label="提现金额" min-width="120" />
            <el-table-column label="提现方式" min-width="100">
                <template slot-scope="scope">
                    <span>{{ scope.row.extract_type | extractTypeFilter }}</span>
                </template>
            </el-table-column>
            <el-table-column label="银行名称" min-width="100">
                <template slot-scope="scope">
                    <span>{{ scope.row.extract_type === 0 ? scope.row.bank_address : '-' }}</span>
                </template>
            </el-table-column>
            <el-table-column label="账号" min-width="100">
                <template slot-scope="scope">
                    <span v-if="scope.row.extract_type==0">{{scope.row.bank_code }}</span>
                    <span v-else-if="scope.row.extract_type==2">{{scope.row.alipay_code }}</span>
                    <span v-else-if="scope.row.extract_type==1">{{scope.row.wechat }}</span>
                    <span v-else>已退款</span>
                </template>
            </el-table-column>
            <el-table-column label="审核状态" min-width="200">
                <template slot-scope="scope">
                    <span class="spBlock">{{ scope.row.status | extractStatusFilter }}</span>
                    <template v-if="scope.row.status === 0">
                        <el-button type="danger" icon="el-icon-close" size="mini" @click="onExamine(scope.row.extract_id)">未通过</el-button>
                        <el-button type="primary" icon="el-icon-check" size="mini" @click="ok(scope.row.extract_id)">通过</el-button>
                    </template>
                </template>
            </el-table-column>
            <el-table-column label="拒绝原因" min-width="150">
                <template slot-scope="scope">
                    <span class="spBlock">{{ scope.row.fail_msg ? scope.row.fail_msg : '-' }}</span>
                </template>
            </el-table-column>
            <el-table-column label="备注" min-width="200">
                <template slot-scope="scope">
                    <span class="spBlock">{{ scope.row.mark | filterEmpty }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="create_time" label="添加时间" min-width="150" />
        </el-table>
        <div class="block">
            <el-pagination :page-sizes="[20, 40, 60, 80]" :page-size="tableFrom.limit" :current-page="tableFrom.page" layout="total, sizes, prev, pager, next, jumper" :total="tableData.total" @size-change="handleSizeChange" @current-change="pageChange" />
        </div>
    </el-card>
</div>
</template>

<script>
import {
    extractListApi,
    extractStatusApi
} from '@/api/accounts'
import {
    fromList
} from '@/libs/constants.js'
export default {
    name: 'AccountsExtract',
    data() {
        return {
            timeVal: [],
            tableData: {
                data: [],
                total: 0
            },
            listLoading: true,
            tableFrom: {
                extract_type: '',
                status: '',
                date: '',
                keyword: '',
                page: 1,
                limit: 20
            },
            fromList: fromList
        }
    },
    mounted() {
        this.getList()
    },
    methods: {
        onExamine(id) {
            this.$prompt('未通过', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                inputErrorMessage: '请输入原因',
                inputType: 'textarea',
                inputValue: '输入信息不完整或有误!',
                inputPlaceholder: '请输入原因',
                inputValidator: (value) => {
                    if (!value) {
                        return '请输入原因'
                    }
                }
            }).then(({
                value
            }) => {
                extractStatusApi(id, {
                    status: -1,
                    fail_msg: value
                }).then(res => {
                    this.$message({
                        type: 'success',
                        message: '提交成功'
                    })
                    this.getList()
                }).catch((res) => {
                    this.$message.error(res.message)
                })
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '取消输入'
                })
            })
        },
        ok(id) {
            this.$modalSure('审核通过吗').then(() => {
                extractStatusApi(id, {
                    status: 1
                }).then(({
                    message
                }) => {
                    this.$message.success(message)
                    this.getList()
                }).catch(({
                    message
                }) => {
                    this.$message.error(message)
                })
            })
        },
        // 选择时间
        selectChange(tab) {
            this.timeVal = []
            this.tableFrom.date = tab
            this.tableFrom.page = 1;
            this.getList()
        },
        // 具体日期
        onchangeTime(e) {
            this.timeVal = e
            this.tableFrom.date = e ? this.timeVal.join('-') : ''
            this.tableFrom.page = 1;
            this.getList()
        },
        // 列表
        getList(num) {
            this.listLoading = true
            this.tableFrom.page = num ? num : this.tableFrom.page;
            extractListApi(this.tableFrom).then(res => {
                this.tableData.data = res.data.list
                this.tableData.total = res.data.count
                this.listLoading = false
            }).catch((res) => {
                this.$message.error(res.message)
                this.listLoading = false
            })
        },
        pageChange(page) {
            this.tableFrom.page = page
            this.getList()
        },
        handleSizeChange(val) {
            this.tableFrom.limit = val
            this.getList()
        }
    }
}
</script>

<style scoped>
.selWidth {
    width: 300px;
}
</style>
