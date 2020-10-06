<template>
  <div class="divBox">
    <base-info ref="baseInfo" class="mb15" />
    <to-day class="mb15" />
    <el-row :gutter="20" class="mb15">
      <el-col v-bind="grid">
        <my-ranking :merchant-data="merchantStock" :mer-title="merTitle" @getList="getList" />
      </el-col>
      <el-col v-bind="grid">
        <my-ranking :mer-title="visitTitle" :merchant-data="merchantVisit" @getList="getVisit" />
      </el-col>
      <el-col v-bind="grid">
        <merchant-rate />
      </el-col>
    </el-row>
    <user-data class="mb15" />
    <user-from />
  </div>
</template>

<script>
import { merchantStockApi, merchantVisitApi } from '@/api/home'
import { checkAuthApi, authTypeApi } from '@/api/maintain'
import { mapGetters } from 'vuex'
import baseInfo from './components/baseInfo'
import toDay from './components/toDay'
import myRanking from './components/ranking'
import merchantRate from './components/merchantRate'
import userData from './components/userData'
import userFrom from './components/user'
import Cookies from 'js-cookie'
export default {
  name: 'Dashboard',
  components: { baseInfo, toDay, myRanking, merchantRate, userData, userFrom },
  data() {
    return {
      merTitle: '商品销量排行',
      visitTitle: '商户访客量排行',
      currentRole: 'adminDashboard',
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 24
      },
      merchantStock: [],
      merchantVisit: []
    }
  },
  computed: {
    ...mapGetters([
      'roles'
    ])
  },
  mounted() {
    console.log(!Cookies.get('auth'))
    this.getAuth()
    if (!Cookies.get('auth')) {
      checkAuthApi().then(res => {
        if (res.message !== 'success') {
          return this.$notify.warning({
            title: '授权提醒',
            duration: 0,
            dangerouslyUseHTMLString: true,
            message: res.message,
            render: h => {
              return h('div', [
                h('a', {
                  attrs: {
                    href: 'http://www.crmeb.com/home/grant/applyauthorize.html',
                    target: '_blank'
                  }
                }, res.message)
              ])
            },
            onClose() {
              Cookies.set('auth', true)
            }
          })
        }
      }).catch(res => {
      })
    }else{
      this.getList('lately30')
      this.getVisit('lately30')
    }
  },
  methods: {
    getAuth() {
      authTypeApi().then(res => {
        const data = res.data || {}
        if (data.auth_code && data.auth) {
          this.authCode = data.auth_code
          this.auth = true
        }
      }).catch(({ message }) => {
        this.$message.error(message)
      })
    },
    // 商品销量
    getList(val) {
      merchantStockApi({ date: val }).then(res => {
        this.merchantStock = res.data.list
      }).catch(res => {
        this.$message.error(res.message)
      })
    },
    // 商户访客量
    getVisit(val) {
      merchantVisitApi({ date: val }).then(res => {
        this.merchantVisit = res.data.list
      }).catch(res => {
        this.$message.error(res.message)
      })
    }
  }
}
</script>
