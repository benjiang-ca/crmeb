<template>
<div class="divBox">
    <el-card class="box-card">
        <div slot="header" class="clearfix">
            <!--<el-tabs v-model="tableFrom.type" @tab-click="getList">-->
            <!--<el-tab-pane label="全部订单" name="" />-->
            <!--<el-tab-pane label="普通订单" name="1" />-->
            <!--<el-tab-pane label="拼团订单" name="2" />-->
            <!--<el-tab-pane label="秒杀订单" name="3" />-->
            <!--<el-tab-pane label="砍价订单" name="4" />-->
            <!--</el-tabs>-->
            <div class="container">
                <el-form size="small" label-width="100px">
                    <el-form-item label="订单状态：">
                        <el-radio-group v-model="tableFrom.status" type="button" @change="getList(1)">
                            <el-radio-button label>全部 {{ '(' +orderChartType.all?orderChartType.all:0 + ')' }}</el-radio-button>
                            <el-radio-button label="1">待付款 {{ '(' +orderChartType.unpaid?orderChartType.unpaid:0+ ')' }}</el-radio-button>
                            <el-radio-button label="2">待发货 {{ '(' +orderChartType.unshipped?orderChartType.unshipped:0+ ')' }}</el-radio-button>
                            <el-radio-button label="3">待收货 {{ '(' +orderChartType.untake?orderChartType.untake:0+ ')' }}</el-radio-button>
                            <el-radio-button label="4">待评价 {{ '(' +orderChartType.unevaluate?orderChartType.unevaluate:0+ ')' }}</el-radio-button>
                            <el-radio-button label="5">交易完成 {{ '(' +orderChartType.complete?orderChartType.complete:0+ ')' }}</el-radio-button>
                            <el-radio-button label="6">已退款 {{ '(' +orderChartType.refund?orderChartType.refund:0+ ')' }}</el-radio-button>
                            <el-radio-button label="7">已删除 {{ '(' +orderChartType.del?orderChartType.del:0+ ')' }}</el-radio-button>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="时间选择：" class="width100">
                        <el-radio-group v-model="tableFrom.date" type="button" class="mr20" size="small" @change="selectChange(tableFrom.date)">
                            <el-radio-button v-for="(item,i) in fromList.fromTxt" :key="i" :label="item.val">{{ item.text }}</el-radio-button>
                        </el-radio-group>
                        <el-date-picker v-model="timeVal" value-format="yyyy/MM/dd" format="yyyy/MM/dd" size="small" type="daterange" placement="bottom-end" placeholder="自定义时间" style="width: 250px;" @change="onchangeTime" />
                    </el-form-item>
                    <el-form-item label="关键字：" class="width100">
                        <el-input v-model="tableFrom.keywords" placeholder="请输入订单号/收货人/联系方式" class="selWidth" size="small">
                            <el-button slot="append" icon="el-icon-search" size="small" @click="getList(1)" />
                        </el-input>
                        <el-button size="small" type="primary" @click="exportOrder">生成列表</el-button>
                        <el-button size="small" type="primary" @click="getExportFileList">导出已生成列表</el-button>
                    </el-form-item>
                    <el-form-item label="用户信息：" class="width100">
                        <el-input v-model="tableFrom.username" placeholder="请输入用户昵称/手机号" class="selWidth" size="small">
                            <el-button slot="append" icon="el-icon-search" size="small" @click="getList(1)" />
                        </el-input>
                        <el-button size="small" type="primary" @click="orderCancellation">订单核销</el-button>
                    </el-form-item>
                </el-form>
            </div>
            <el-tabs v-if="headeNum.length > 0" v-model="tableFrom.order_type" @tab-click="getList(1)">
                <el-tab-pane v-for="(item,index) in headeNum" :key="index" :name="item.order_type.toString()" :label="item.title +'('+item.count +')' " />
            </el-tabs>
            <cards-data :card-lists="cardLists" />
        </div>
        <el-table v-loading="listLoading" :data="tableData.data" style="width: 100%" size="mini" class="table" highlight-current-row :cell-class-name="addTdClass">
            <el-table-column type="expand">
                <template slot-scope="props">
                    <el-form label-position="left" inline class="demo-table-expand">
                        <el-form-item label="商品总价：">
                            <span>{{ props.row.total_price | filterEmpty }}</span>
                        </el-form-item>
                        <el-form-item label="下单时间：">
                            <span>{{ props.row.create_time}}</span>
                        </el-form-item>
                        <el-form-item label="用户备注：">
                            <span style="display: inline-block; width: 200px;">{{ props.row.mark | filterEmpty }}</span>
                        </el-form-item>
                        <el-form-item label="商家备注：">
                            <span>{{ props.row.remark | filterEmpty }}</span>
                        </el-form-item>
                    </el-form>
                </template>
            </el-table-column>
            <el-table-column label="订单编号" min-width="170">
                <template slot-scope="scope">
                    <span style="display: block;" v-text="scope.row.order_sn" />
                    <span v-show="scope.row.is_del > 0" style="color: #ED4014;display: block;">用户已删除</span>
                </template>
            </el-table-column>
            <el-table-column label="订单类型" min-width="100">
                <template slot-scope="scope">
                    <span>{{ scope.row.order_type == 0 ? "普通订单" : "核销订单" }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="real_name" label="收货人" min-width="130" />
            <el-table-column label="商品信息" min-width="330">
                <template slot-scope="scope">
                    <div v-for="(val, i ) in scope.row.orderProduct" :key="i" class="tabBox acea-row row-middle">
                        <div class="demo-image__preview">
                            <el-image :src="val.cart_info.product.image" :preview-src-list="[val.cart_info.product.image]" />
                        </div>
                        <span class="tabBox_tit">{{ val.cart_info.product.store_name + ' | ' }}{{ val.cart_info.productAttr.sku }}</span>
                        <span class="tabBox_pice">
                            {{ '￥'+ val.cart_info.productAttr.price + ' x '+ val.product_num }}
                            <em v-if="val.refund_num < val.product_num && val.refund_num >= 0" style="color: red;font-style: normal;">(-{{ val.product_num - val.refund_num }})</em>
                        </span>
                    </div>
                </template>
            </el-table-column>
            <el-table-column prop="pay_price" label="实际支付" min-width="100" />
            <el-table-column label="支付类型" min-width="80">
                <template slot-scope="scope">
                    <span v-if="scope.row.paid === 1">{{ scope.row.pay_type == 0 ? "余额支付" : "微信支付" }}</span>
                    <span v-else>--</span>
                </template>
            </el-table-column>
            <el-table-column label="支付状态" min-width="80">
                <template slot-scope="scope">
                    <span>{{ scope.row.paid === 0 ? "未支付" : "已支付" }}</span>
                </template>
            </el-table-column>
            <el-table-column label="订单状态" min-width="100">
                <template slot-scope="scope">
                    <span v-if="scope.row.is_del === 0">
                        <span v-if="scope.row.order_type === 0">{{ scope.row.status | orderStatusFilter }}</span>
                        <span v-else>{{ scope.row.status | takeOrderStatusFilter }}</span>
                    </span>
                    <span v-else>已删除</span>
                </template>
            </el-table-column>
            <el-table-column prop="create_time" label="下单时间" min-width="130" />
            <el-table-column key="8" label="操作" min-width="150" fixed="right" align="center">
                <template slot-scope="scope">
                    <!--<span v-for="(val, i ) in scope.row.orderProduct" :key="i">
                        <el-button v-if="orderFilter(scope.row) " type="text" size="small" @click="onRefundDetail(scope.row.order_sn)">查看退款单</el-button>
                    </span>-->
                    <el-button v-if="orderFilter(scope.row) " type="text" size="small" @click="onRefundDetail(scope.row.order_sn)">查看退款单</el-button>
                    <el-button v-if="scope.row.paid === 0 && scope.row.is_del===0" type="text" size="small" class="mr10" @click="edit(scope.row.order_id)">编辑</el-button>
                    <el-button v-if="scope.row.order_type == 0 && scope.row.status === 0 && scope.row.paid === 1" type="text" size="small" class="mr10" @click="send(scope.row.order_id)">发送货</el-button>
                    <el-button v-if="scope.row.status> 0 || scope.row.is_del=== 1" type="text" size="small" class="mr10" @click="onOrderDetails(scope.row.order_id)">订单详情</el-button>
                    <el-button v-if="scope.row.order_type == 1 && scope.row.paid == 1 && scope.row.status == 0" type="text" size="small" class="mr10" @click="handleCancellation(scope.row.verify_code)">去核销</el-button>
                    <el-dropdown trigger="click">
                        <span class="el-dropdown-link">
                            更多
                            <i class="el-icon-arrow-down el-icon--right" />
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-if="(scope.row.paid===1 && scope.row.order_type===0)" @click.native="printOrder(scope.row.order_id)">订单打印</el-dropdown-item>
                            <el-dropdown-item v-if="(scope.row.status < 1 && scope.row.is_del===0)" @click.native="onOrderDetails(scope.row.order_id)">订单详情</el-dropdown-item>
                            <el-dropdown-item @click.native="onOrderLog(scope.row.order_id)">订单记录</el-dropdown-item>
                            <el-dropdown-item @click.native="onOrderMark(scope.row.order_id)">订单备注</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.is_del !== 0" @click.native="handleDelete(scope.row, scope.$index)">删除订单</el-dropdown-item>
<!--                          <span v-show="scope.row.is_del > 0" style="color: #ED4014;display: block;">用户已删除</span>-->
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>
        <div class="block">
            <el-pagination :page-sizes="[20, 40, 60, 80]" :page-size="tableFrom.limit" :current-page="tableFrom.page" layout="total, sizes, prev, pager, next, jumper" :total="tableData.total" @size-change="handleSizeChange" @current-change="pageChange" />
        </div>
    </el-card>

    <!--记录-->
    <el-dialog title="操作记录" :visible.sync="dialogVisible" width="700px">
        <el-table v-loading="LogLoading" border :data="tableDataLog.data" style="width: 100%">
            <el-table-column prop="order_id" align="center" label="订单ID" min-width="80" />
            <el-table-column prop="change_message" label="操作记录" align="center" min-width="280" />
            <el-table-column prop="change_time" label="操作时间" align="center" min-width="280" />
        </el-table>
        <div class="block">
            <el-pagination :page-sizes="[20, 40, 60, 80]" :page-size="tableFromLog.limit" :current-page="tableFromLog.page" layout="total, sizes, prev, pager, next, jumper" :total="tableDataLog.total" @size-change="handleSizeChangeLog" @current-change="pageChangeLog" />
        </div>
    </el-dialog>

    <!--详情-->
    <details-from ref="orderDetail" />
    <!--导出订单列表-->
    <file-list ref="exportList" />
</div>
</template>

<script>
import {
    orderListApi,
    chartApi,
    orderUpdateApi,
    orderDeliveryApi,
    orderLogApi,
    orderDeleteApi,
    orderRemarkApi,
    orderPrintApi,
    exportOrderApi,
    orderCancellationApi,
    orderHeadListApi,
} from "@/api/order";
import detailsFrom from "./orderDetail";
import fileList from "@/components/exportFile/fileList";
import cardsData from "@/components/cards/index";

export default {
    components: {
        detailsFrom,
        cardsData,
        fileList
    },
    data() {
        return {
            orderId: 0,
            tableData: {
                data: [],
                total: 0,
            },
            listLoading: true,
            tableFrom: {
                order_sn: this.$route.query.order_sn ? this.$route.query.order_sn : "",
                order_type: "-1",
                keywords: "",
                status: "",
                date: "",
                page: 1,
                limit: 20,
                type: "1",
                username: "",
            },
            orderChartType: {},
            timeVal: [],
            fromList: {
                title: "选择时间",
                custom: true,
                fromTxt: [{
                        text: "全部",
                        val: ""
                    },
                    {
                        text: "今天",
                        val: "today"
                    },
                    {
                        text: "昨天",
                        val: "yesterday"
                    },
                    {
                        text: "最近7天",
                        val: "lately7"
                    },
                    {
                        text: "最近30天",
                        val: "lately30"
                    },
                    {
                        text: "本月",
                        val: "month"
                    },
                    {
                        text: "本年",
                        val: "year"
                    },
                ],
            },
            selectionList: [],
            ids: "",
            tableFromLog: {
                page: 1,
                limit: 10,
            },
            tableDataLog: {
                data: [],
                total: 0,
            },
            LogLoading: false,
            dialogVisible: false,
            fileVisible: false,
            cardLists: [],
            orderDatalist: null,
            headeNum: [],
        };
    },
    mounted() {
        if (this.$route.query.hasOwnProperty("order_sn")) {
            this.tableFrom.order_sn = this.$route.query.order_sn;
        } else {
            this.tableFrom.order_sn = "";
        }
        this.headerList();
        this.getList(1);
        this.getHeaderList();
    },
    methods: {
        // 头部
        getHeaderList() {
            orderHeadListApi()
                .then((res) => {
                    this.headeNum = res.data;
                })
                .catch((res) => {
                    this.$message.error(res.message);
                });
        },
        // 订单筛选
        orderFilter(item) {
            let status = false;
            item.orderProduct.forEach((el) => {
                if (el.refund_num < el.product_num) {
                    status = true;
                }
            });
            return status;
        },
        // 退款详情页
        onRefundDetail(sn) {
            this.$router.push({
                path: "refund",
                query: {
                    sn: sn,
                },
            });
        },
        // 表格某一行添加特定的样式
        addTdClass(val) {
            if (val.row.status > 0 && val.row.paid == 1) {
                for (let i = 0; i < val.row.orderProduct.length; i++) {
                    if (val.row.orderProduct[i].refund_num >= 0 && val.row.orderProduct[i].refund_num < val.row.orderProduct[i].product_num) {
                        return "row-bg";
                    }
                }
            } else {
                return " ";
            }
        },
        // 详情
        onOrderDetails(id) {
            this.orderId = id;
            this.$refs.orderDetail.dialogVisible = true;
            this.$refs.orderDetail.onOrderDetails(id);
        },
        // 导出
        exportOrder() {
            exportOrderApi(this.tableFrom)
                .then((res) => {
                    // this.$message.success(res.message);
                    // this.fileVisible = true;
                    // this.$refs.exportList.exportFileList();
                  const h = this.$createElement;
                  this.$msgbox({
                    title: '提示',
                    message: h('p', null, [
                      h('span', null, '文件正在生成中，请稍后点击"'),
                      h('span', { style: 'color: teal' }, '导出已生成列表'),
                      h('span', null, '"查看~ '),
                    ]),
                    confirmButtonText: '我知道了',
                  }).then(action => {

                  });                })
                .catch((res) => {
                    this.$message.error(res.message);
                });
        },
        getExportFileList() {
            this.fileVisible = true;
            this.$refs.exportList.exportFileList('order');
        },
        // 订单核销
        orderCancellation() {
            let that = this;
            this.$prompt("", "提示", {
                    confirmButtonText: "立即核销",
                    cancelButtonText: "取消",
                    inputPattern: /\S/,
                    inputPlaceholder: "请输入核销码",
                    inputErrorMessage: "请输入核销码",
                })
                .then(({
                    value
                }) => {
                    that.handleCancellation(value);
                })
                .catch(() => {
                    this.$message({
                        type: "info",
                        message: "取消输入",
                    });
                });
        },
        // 去核销
        handleCancellation(code) {
            this.$confirm("确定核销此订单?", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning",
                })
                .then(() => {
                    orderCancellationApi(code)
                        .then((res) => {
                            this.$message.success(res.message);
                            this.getList('');
                        })
                        .catch((res) => {
                            this.$message.error(res.message);
                            this.LogLoading = false;
                        });
                })
                .catch(() => {
                    this.$message({
                        type: "info",
                        message: "已取消核销",
                    });
                });
        },
        // 订单记录
        onOrderLog(id) {
            this.dialogVisible = true;
            this.LogLoading = true;
            orderLogApi(id, this.tableFromLog)
                .then((res) => {
                    this.tableDataLog.data = res.data.list;
                    this.tableDataLog.total = res.data.count;
                    this.LogLoading = false;
                })
                .catch((res) => {
                    this.$message.error(res.message);
                    this.LogLoading = false;
                });
        },
        pageChangeLog(page) {
            this.tableFromLog.page = page;
            this.getList('');
        },
        handleSizeChangeLog(val) {
            this.tableFromLog.limit = val;
            this.getList('');
        },
        // 打印订单
        printOrder(id) {
            orderPrintApi(id)
                .then((res) => {
                    this.$message.success(res.message);
                })
                .catch((res) => {
                    this.$message.error(res.message);
                });
        },
        // 订单删除
        handleDelete(row, idx) {
            if (row.is_del === 1) {
                this.$modalSure().then(() => {
                    orderDeleteApi(row.order_id)
                        .then(({
                            message
                        }) => {
                            this.$message.success(message);
                            this.tableData.data.splice(idx, 1);
                        })
                        .catch(({
                            message
                        }) => {
                            this.$message.error(message);
                        });
                });
            } else {
                this.$confirm(
                    "您选择的的订单存在用户未删除的订单，无法删除用户未删除的订单！",
                    "提示", {
                        confirmButtonText: "确定",
                        type: "error",
                    }
                );
            }
        },
        // 备注
        onOrderMark(id) {
            this.$modalForm(orderRemarkApi(id)).then(() => this.getList(''));
        },

        handleSelectionChange(val) {
            this.selectionList = val;
            const data = [];
            this.selectionList.map((item) => {
                data.push(item.id);
            });
            this.ids = data.join(",");
        },
        // 选择时间
        selectChange(tab) {
            this.timeVal = [];
            this.tableFrom.date = tab;
            this.getList(1);
        },
        // 具体日期
        onchangeTime(e) {
            this.timeVal = e;
            this.tableFrom.date = e ? this.timeVal.join("-") : "";
            this.getList(1);
        },
        // 编辑
        edit(id) {
            this.$modalForm(orderUpdateApi(id)).then(() => this.getList(''));
        },
        // 发货
        send(id) {
            this.$modalForm(orderDeliveryApi(id)).then(() => this.getList(''));
        },
        // 列表
        getList(num) {
            this.listLoading = true;
            this.tableFrom.page = num ? num : this.tableFrom.page;
            orderListApi(this.tableFrom)
                .then((res) => {
                    this.tableData.data = res.data.list;
                    this.tableData.total = res.data.count;
                    this.cardLists = res.data.stat;
                    this.listLoading = false;
                })
                .catch((res) => {
                    this.$message.error(res.message);
                    this.listLoading = false;
                });
        },
        pageChange(page) {
            this.tableFrom.page = page;
            this.getList('');
        },
        handleSizeChange(val) {
            this.tableFrom.limit = val;
            this.getList('');
        },
        headerList() {
            chartApi()
                .then((res) => {
                    this.orderChartType = res.data;
                })
                .catch((res) => {
                    this.$message.error(res.message);
                });
        }
    }
};
</script>

<style lang="scss" scoped>
.selWidth {
    width: 300px;
}

.el-dropdown-link {
    cursor: pointer;
    color: #409eff;
    font-size: 12px;
}

.el-icon-arrow-down {
    font-size: 12px;
}

.tabBox_tit {
    width: 53%;
    font-size: 12px !important;
    margin: 0 2px 0 10px;
    letter-spacing: 1px;
    padding: 5px 0;
    box-sizing: border-box;
}

/deep/.row-bg {
    .cell {
        color: red !important;
    }
}
</style>
