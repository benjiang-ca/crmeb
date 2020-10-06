<template>
<div class="divBox">
    <el-card class="box-card">
        <div slot="header" class="clearfix">
            <div class="container">
                <el-form size="small" label-width="100px" :inline="true">
                    <el-form-item label="选择时间：" style="width: 100%">
                        <el-radio-group v-model="tableFrom.date" size="small" @change="selectChange(tableFrom.date)">
                            <el-radio-button v-for="(itemn,indexn) in fromList.fromTxt" :key="indexn" :label="itemn.val">{{ itemn.text }}</el-radio-button>
                        </el-radio-group>
                        <el-date-picker v-model="timeVal" type="daterange" placeholder="选择日期" format="yyyy/MM/dd" value-format="yyyy/MM/dd" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" @change="onchangeTime" />
                    </el-form-item>
                    <el-form-item label="关键字：">
                        <el-input v-model="tableFrom.keyword" placeholder="请输入商户名称关键字/联系电话" class="selWidth">
                            <el-button slot="append" icon="el-icon-search" @click="getList(1)" />
                        </el-input>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <el-table v-loading="listLoading" :data="tableData.data" style="width: 100%" size="small" highlight-current-row class="switchTable">
            <el-table-column prop="mer_intention_id" label="ID" min-width="60" />
            <el-table-column prop="mer_name" label="商户名称" min-width="150" />
            <el-table-column prop="merchant_category_id" label="商户分类" min-width="150" />
            <el-table-column prop="name" label="商户姓名" min-width="100" />
            <el-table-column prop="phone" label="联系方式" min-width="100" />
            <el-table-column prop="create_time" label="申请时间" min-width="150" />
            <el-table-column prop="create_time" label="资质图片" min-width="150">
                <template slot-scope="scope">
                    <div class="demo-image__preview">
                        <el-image v-for="(item,index) in scope.row.images" :key="index" :src="item" class="mr5" :preview-src-list="[item]" />
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="状态" min-width="150">
                <template slot-scope="scope">
                    <el-tag v-if="scope.row.status == 1" type="success">通过</el-tag>
                    <el-tag v-if="scope.row.status == 0" type="info">未处理</el-tag>
                    <el-tag v-if="scope.row.status == 2" type="warning">未通过</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="mark" label="备注" min-width="150" />
            <el-table-column label="操作" min-width="280" fixed="right" align="center">
                <template slot-scope="scope">
                    <el-button v-if="scope.row.status == 0" type="text" size="small" @click="onchangeIsShow(scope.row.mer_intention_id)">审核</el-button>
                    <el-button type="text" size="small" @click="onEdit(scope.row.mer_intention_id)">备注</el-button>
                    <el-button type="text" size="small" @click="handleDelete(scope.row.mer_intention_id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="block">
            <el-pagination :page-sizes="[20, 40, 60, 80]" :page-size="tableFrom.limit" :current-page="tableFrom.page" layout="total, sizes, prev, pager, next, jumper" :total="tableData.total" @size-change="handleSizeChange" @current-change="pageChange" />
        </div>
    </el-card>
</div>
</template>

<script>
import {
    intentionLstApi,
    auditApi,
    intentionDelte,
    intentionStatusApi,
} from "@/api/merchant";
import {
    fromList
} from "@/libs/constants.js";
import {
    roterPre
} from "@/settings";
export default {
    name: "MerchantApplication",
    data() {
        return {
            fromList: fromList,
            roterPre: roterPre,
            isChecked: false,
            listLoading: true,
            tableData: {
                data: [],
                total: 0,
            },
            tableFrom: {
                page: 1,
                limit: 20,
                date: "",
                status: "",
                keyword: "",
            },
            autoUpdate: true,
            timeVal: [],
        };
    },
    mounted() {
        this.getList('');
    },
    methods: {
        // 选择时间
        selectChange(tab) {
            this.tableFrom.date = tab;
            this.timeVal = [];
            this.tableFrom.page = 1;
            this.getList('');
        },
        // 具体日期
        onchangeTime(e) {
            this.timeVal = e;
            this.tableFrom.date = this.timeVal ? this.timeVal.join("-") : "";
            this.tableFrom.page = 1;
            this.getList('');
        },
        // 列表
        getList(num) {
            this.listLoading = true;
            this.tableFrom.page = num ? num : this.tableFrom.page;
            intentionLstApi(this.tableFrom)
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
            this.getList('');
        },
        handleSizeChange(val) {
            this.tableFrom.limit = val;
            this.getList(1);
        },
        // 修改状态
        onchangeIsShow(id) {
            this.$modalForm(intentionStatusApi(id)).then(() => this.getList(''));
        },
        // 添加

        // 编辑
        onEdit(id) {
            this.$modalForm(auditApi(id)).then(() => this.getList(''));
        },
        // 删除
        handleDelete(id) {
            this.$modalSure().then(() => {
                intentionDelte(id)
                    .then(({
                        message
                    }) => {
                        this.$message.success(message);
                        this.getList('');
                    })
                    .catch(({
                        message
                    }) => {
                        this.$message.error(message);
                    });
            });
        },
    },
};
</script>

<style lang="scss" scoped>
/deep/ table .el-image {
    display: inline-block !important;
}
</style>
