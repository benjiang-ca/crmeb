<template>
<el-row :gutter="20" class="mb15">
    <el-col v-if="users" :xl="16" :lg="24" :md="24" :sm="24" :xs="24">
        <el-card class="box-card">
            <div class="acea-row row-between-wrapper mb20">
                <span class="header-title">成交用户</span>
                <span class="header-time">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-date mr5" />{{ name }}<i class="el-icon-arrow-down el-icon--right" />
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-for="(item,i) in fromList.fromTxt" :key="i" @click.native="setTime(item.val, item.text)">{{ item.text }}</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </span>
            </div>
            <div class="user">
                <div class="acea-row">
                    <div class="user-visitUser">
                        <span class="spBlock mb10">访客人数</span>
                        <span class="spBlock" v-text="users.visitUser" />
                    </div>
                    <div class="user-visitUser-ti">访客</div>
                </div>
                <div class="orderUser">
                    <div class="user-orderUser acea-row">
                        <div class="mr50">
                            <span class="spBlock mb10">下单人数</span>
                            <span class="spBlock" v-text="users.orderUser" />
                        </div>
                        <div>
                            <span class="spBlock mb10">下单金额</span>
                            <span class="spBlock" v-text="users.orderPrice" />
                        </div>
                    </div>
                    <div class="user-orderUser-ti">下单</div>
                    <div :class="fullWidth>1046?'user-orderUser-change':'user-orderUser-changeduan'" />
                    <div>
                        <span class="spBlock sp1">访客-下单转化率：{{ Math.floor(users.orderRate*100) }} %</span>
                        <span class="spBlock sp2">下单-下单转化率：{{ Math.floor(users.payOrderRate*100) }} %</span>
                    </div>
                </div>
                <div class="acea-row payOrderUser">
                    <div class="user-payOrderUser acea-row">
                        <div class="mr50">
                            <span class="spBlock mb10">支付人数</span>
                            <span class="spBlock" v-text="users.payOrderUser" />
                        </div>
                        <div class="mr50">
                            <span class="spBlock mb10">支付金额</span>
                            <span class="spBlock" v-text="users.payOrderPrice" />
                        </div>
                        <div>
                            <span class="spBlock mb10">客单价</span>
                            <span class="spBlock" v-text="users.userRate" />
                        </div>
                    </div>
                    <div class="user-payOrderUser-ti">支付</div>
                </div>
            </div>
        </el-card>
    </el-col>
    <el-col :xl="8" :lg="24" :md="24" :sm="24" :xs="24">
        <el-card class="box-card" style="height: 358px;">
            <div class="acea-row row-between-wrapper mb20" style="padding-bottom: 30px;">
                <span class="header-title">成交用户占比</span>
                <span class="header-time">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-date mr5" />{{ nameRate }}<i class="el-icon-arrow-down el-icon--right" />
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-for="(item,i) in fromList.fromTxt" :key="i" @click.native="setTimeRate(item.val, item.text)">{{ item.text }}</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </span>
            </div>
            <el-radio-group v-model="rateType" size="mini" class="echart-btn" style="float: right" @change="changeRate(rateType)">
                <el-radio-button label="price">金额</el-radio-button>
                <el-radio-button label="user">客户数</el-radio-button>
            </el-radio-group>
            <echarts-from ref="visitChart" height="100%" width="100%" :option-data="optionData" :styles="style" v-if="usersRate" />
        </el-card>
    </el-col>
</el-row>
</template>

<script>
import {
    merchantUserApi,
    userRateApi
} from '@/api/home'
import echartsFrom from '@/components/echarts/index'
export default {
    name: 'User',
    components: {
        echartsFrom
    },
    data() {
        return {
            fullWidth: document.body.clientWidth,
            style: {
                height: '200px'
            },
            name: '最近30天',
            users: null,
            nameRate: '最近30天',
            nameVal: 'lately30',
            usersRate: {},
            rateType: 'price',
            optionData: {},
            fromList: {
                title: '选择时间',
                custom: true,
                fromTxt: [{
                        text: '今天',
                        val: 'today'
                    },
                    {
                        text: '昨天',
                        val: 'yesterday'
                    },
                    {
                        text: '最近7天',
                        val: 'lately7'
                    },
                    {
                        text: '最近30天',
                        val: 'lately30'
                    },
                    {
                        text: '本月',
                        val: 'month'
                    },
                    {
                        text: '本年',
                        val: 'year'
                    }
                ]
            }
        }
    },
    mounted() {
        this.getList('lately30')
        this.getRate('lately30')
    },
    created() {
        window.addEventListener('resize', this.handleResize)
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.handleResize)
    },
    methods: {
        handleResize(event) {
            this.fullWidth = document.body.clientWidth
        },
        setTime(val, text) {
            this.name = text
            this.getList(val)
        },
        getList(val) {
            merchantUserApi({
                date: val
            }).then(res => {
                this.users = res.data
            }).catch(res => {
                this.$message.error(res.message)
            })
        },
        setTimeRate(val, text) {
            this.nameVal = val;
            this.nameRate = text
            this.getRate(val)
        },
        changeRate() {
            this.getRate(this.nameVal)
        },
        getRate(val) {
            userRateApi({
                date: val
            }).then(res => {
                let seriesData = []
                this.usersRate = res.data
                this.rateType === 'price' ? seriesData = [{
                    value: this.usersRate.newTotalPrice,
                    name: '新成交用户',
                    itemStyle: {
                        normal: {
                            color: '#4ECB73'
                        }
                    }
                }, {
                    value: this.usersRate.oldTotalPrice,
                    name: '老用户',
                    itemStyle: {
                        normal: {
                            color: '#39A1FF'
                        }
                    }
                }] : seriesData = [{
                    value: this.usersRate.newUser,
                    name: '新成交用户',
                    itemStyle: {
                        normal: {
                            color: '#4ECB73'
                        }
                    }
                }, {
                    value: this.usersRate.oldUser,
                    name: '老用户',
                    itemStyle: {
                        normal: {
                            color: '#39A1FF'
                        }
                    }
                }]
                this.optionData = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{a} <br/>{b} : {c} ({d}%)'
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left',
                        data: ['新成交用户', '老用户']
                    },
                    series: [{
                        name: '访问来源',
                        type: 'pie',
                        radius: '65%',
                        center: ['50%', '65%'],
                        data: seriesData,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }]
                }
            }).catch(res => {
                this.$message.error(res.message)
            })
        }
    }
}
</script>

<style lang="scss" scoped>
/deep/ .el-card__body {
    position: relative;
}

.echart-btn {
    position: absolute;
    right: 20px;
    top: 45px;
}

.sp1 {
    margin-left: 10px;
    overflow: auto;
    margin-top: -9px;
}

.sp2 {
    margin-top: 66px;
    margin-left: 10px;
    overflow: auto;

    white-space: nowrap;
    /*overflow: hidden;*/
    text-overflow: ellipsis;
}

.orderUser {
    position: relative;
    top: -6px;
    display: flex;
    white-space: normal;
}

.payOrderUser {
    position: relative;
    top: -16px;
}

.user {
    min-width: 900px;

    &-visitUser {
        width: 55%;
        height: 84px;
        background: rgba(99, 149, 250, 0.1);
        padding: 18px 0 18px 17px;
        box-sizing: border-box;

        &-ti {
            width: 285px;
            height: 84px;
            background: #5B8FF9;
            -webkit-transform: perspective(5em) rotateX(-20deg);
            transform: perspective(5em) rotateX(-20deg);
            margin-left: -104px;
            margin-top: 8px;
            text-align: center;
            line-height: 70px;
            color: #fff;
            font-size: 14px;
        }
    }

    &-orderUser {
        width: 55%;
        height: 84px;
        background: rgba(99, 218, 171, 0.1);
        padding: 18px 0 18px 17px;
        box-sizing: border-box;

        &-ti {
            width: 180px;
            height: 90px;
            background: #5AD8A6;
            -webkit-transform: perspective(7em) rotateX(-20deg);
            transform: perspective(7em) rotateX(-30deg);
            margin-left: -52px;
            margin-top: 6px;
            text-align: center;
            line-height: 71px;
            color: #fff;
            font-size: 14px;
        }

        &-change {
            height: 83px;
            width: 128px;
            border-bottom: 1px solid #D8D8D8;
            border-top: 1px solid #D8D8D8;
            margin-left: -19px;
        }

        &-changeduan {
            height: 83px;
            /*width: 5%;*/
            border-bottom: 1px solid #D8D8D8;
            border-top: 1px solid #D8D8D8;
            margin-left: -19px;
        }
    }

    &-payOrderUser {
        width: 55%;
        height: 84px;
        background: rgba(101, 119, 152, 0.1);
        padding: 18px 0 18px 17px;
        box-sizing: border-box;

        &-ti {
            width: 109px;
            height: 80px;
            background: #5D7092;
            -webkit-transform: perspective(7em) rotateX(-20deg);
            transform: perspective(3em) rotateX(-15deg);
            margin-left: -18px;
            margin-top: 12px;
            text-align: center;
            line-height: 61px;
            color: #fff;
            font-size: 14px;
        }
    }
}
</style>
