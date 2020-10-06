<template>
<el-card v-if="statisticsData" class="box-card statistics">
    <div class="acea-row row-between-wrapper mb20">
        <span class="header-title">运营数据</span>
        <span class="header-time">{{ getdate() }}</span>
    </div>
    <div class="content">
        <span class="content-title spBlock">支付订单金额</span>
        <span class="content-number spBlock mb15">{{ statisticsData.today.payPrice }}</span>
        <span class="content-time spBlock">昨日：{{ statisticsData.yesterday.payPrice }}</span>
        <span class="content-time spBlock">同比上周：<i class="content-is" :class="Number(statisticsData.lastWeekRate.payPrice)>=0?'up':'down'">{{ Math.floor(statisticsData.lastWeekRate.payPrice*100*1000/1000) }}%</i><i :class="Number(statisticsData.lastWeekRate.payPrice)>=0?'el-icon-caret-top':'el-icon-caret-bottom'" /></span>
    </div>
    <div class="content pl25">
        <span class="content-title spBlock">新增用户</span>
        <span class="content-number spBlock mb15">{{ statisticsData.today.userNum }}</span>
        <span class="content-time spBlock">昨日：{{ statisticsData.yesterday.userNum }}</span>
        <span class="content-time spBlock">同比上周：<i class="content-is" :class="Number(statisticsData.lastWeekRate.userNum)>=0?'up':'down'">{{ Math.floor(statisticsData.lastWeekRate.userNum*1000/1000) }}%</i><i :class="Number(statisticsData.lastWeekRate.userNum)>=0?'el-icon-caret-top':'el-icon-caret-bottom'" /></span>
    </div>
    <div class="content pl25">
        <span class="content-title spBlock">浏览量</span>
        <span class="content-number spBlock mb15">{{ statisticsData.today.visitNum }}</span>
        <span class="content-time spBlock">昨日：{{ statisticsData.yesterday.visitNum }}</span>
        <span class="content-time spBlock">同比上周：<i class="content-is" :class="Number(statisticsData.lastWeekRate.visitNum)>=0?'up':'down'">{{ Math.floor(statisticsData.lastWeekRate.visitNum*1000/1000) }}%</i><i :class="Number(statisticsData.lastWeekRate.visitNum)>=0?'el-icon-caret-top':'el-icon-caret-bottom'" /></span>
    </div>
    <div class="content pl25">
        <span class="content-title spBlock">访客数</span>
        <span class="content-number spBlock mb15">{{ statisticsData.today.visitUserNum }}</span>
        <span class="content-time spBlock">昨日：{{ statisticsData.yesterday.visitUserNum }}</span>
        <span class="content-time spBlock">同比上周：<i class="content-is" :class="Number(statisticsData.lastWeekRate.visitUserNum)>=0?'up':'down'">{{ Math.floor(statisticsData.lastWeekRate.visitUserNum*100*1000/1000) }}%</i><i :class="Number(statisticsData.lastWeekRate.visitUserNum)>=0?'el-icon-caret-top':'el-icon-caret-bottom'" /></span>
    </div>
    <div class="content pl25" style="border:none">
        <span class="content-title spBlock">店铺数</span>
        <span class="content-number spBlock mb15">{{ statisticsData.today.storeNum }}</span>
        <span class="content-time spBlock">昨日：{{ statisticsData.yesterday.storeNum }}</span>
        <span class="content-time spBlock">同比上周：<i class="content-is" :class="Number(statisticsData.lastWeekRate.storeNum)>=0?'up':'down'">{{ Math.floor(statisticsData.lastWeekRate.storeNum*100*1000/1000) }}%</i><i :class="Number(statisticsData.lastWeekRate.storeNum)>=0?'el-icon-caret-top':'el-icon-caret-bottom'" /></span>
    </div>
</el-card>
</template>

<script>
import {
    statisticsApi
} from '@/api/home'
export default {
    name: 'BaseInfo',
    data() {
        return {
            statisticsData: null
        }
    },
    mounted() {
        this.getList()
    },
    methods: {
        getdate() {
            var date = new Date()
            var year = date.getFullYear()
            var month = date.getMonth() + 1
            var strDate = date.getDate()

            if (month >= 1 && month <= 9) {
                month = '0' + month
            }
            if (strDate >= 0 && strDate <= 9) {
                strDate = '0' + strDate
            }
            var currentdate = year + ' 年 ' + month + ' 月 ' + strDate + ' 日 '
            return currentdate
        },
        getList() {
            this.listLoading = true
            statisticsApi(this.tableFrom).then(res => {
                this.statisticsData = res.data
                this.listLoading = false
            }).catch(res => {
                this.listLoading = false
                this.$message.error(res.message)
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.statistics {
    min-width: 700px;
}

.up,
.el-icon-caret-top {
    color: #F5222D;
    font-size: 12px;
    opacity: 1 !important;
}

.down,
.el-icon-caret-bottom {
    color: #39C15B;
    font-size: 12px;
    opacity: 100% !important;
}

.header {
    &-title {
        font-size: 16px;
        color: #000000;
        font-weight: 500;
    }

    &-time {
        font-size: 12px;
        color: #8C8C8C;
    }
}

.content {
    width: 19%;
    display: inline-block;
    border-right: 1px solid rgba(0, 0, 0, 0.1);

    &-is {
        opacity: 1%;
    }

    &-title {
        font-size: 14px;
        color: #000000;
        margin-bottom: 5px;
    }

    &-time {
        font-size: 12px;
        color: #8C8C8C;
        margin-bottom: 5px;
    }

    &-number {
        font-size: 30px;
    }
}
</style>
