<template>
  <div class="divBox">
    <el-card class="box-card">
      <div class="auth acea-row row-between-wrapper">
        <div class="acea-row row-middle">
          <!--<Icon type="ios-bulb-outline" class="iconIos blue"/>-->
          <i class="el-icon-share iconIos blue" />
          <div v-if="status === -1 || status === -9" class="text">
            <div>体验时间剩余 {{ dayNum }}天</div>
            <div class="code">到期后后台将不能正常使用，如果您对我们的系统满意，请支持正版！</div>
          </div>
          <div v-else-if="status === 2" class="text">
            <div>体验时间剩余 {{ dayNum }}天</div>
            <div class="code red">审核未通过</div>
          </div>
          <div v-else-if="status === 1" class="text">
            <div>商业授权</div>
            <div class="code">授权码：{{ authCode }}</div>
          </div>
          <div v-else-if="status === 0" class="text">
            <div>体验时间剩余 {{ dayNum }}天</div>
            <div class="code blue">授权申请已提交，请等待审核</div>
          </div>
        </div>
        <el-button v-if="status === 1" class="grey" @click="toCrmeb()">进入官网</el-button>
        <el-button v-else-if="status === -1 || status === -9" type="primary" @click="dialogVisible = true">申请授权</el-button>
        <el-button v-else-if="status === 2" type="primary" @click="dialogVisible = true">重新申请</el-button>
        <el-button v-else-if="status === 0" class="grey">审核中</el-button>
      </div>
    </el-card>

    <el-dialog
      :visible.sync="dialogVisible"
      scrollable
      footer-hide
      closable
      title="申请商业授权"
      :z-index="1"
      width="640px"
      :before-close="cancel"
    >
      <div class="article-manager">
        <el-form
          ref="formItem"
          :model="formItem"
          label-width="100px"
          :rules="ruleValidate"
        >
          <el-form-item label="企业名称：" prop="company_name" label-for="company_name">
            <el-input v-model="formItem.company_name" placeholder="请填写您的企业名称" />
          </el-form-item>
          <el-form-item label="企业域名：" prop="domain_name" label-for="domain_name">
            <el-input v-model="formItem.domain_name" placeholder="注：区分二级域名，申请通过后只能使用当前提交的域名" />
          </el-form-item>
          <el-form-item label="订单号：" label-for="order_id" prop="order_id">
            <el-input
              v-model="formItem.order_id"
              placeholder="请输入您在淘宝或小程序购买的源码订单号"
              class="customer"
            >
              <a slot="append" target="_blank" href="http://www.crmeb.com/home/grant/applyauthorize.html">联系客服获取订单号</a>
            </el-input>
          </el-form-item>
          <el-form-item label="手机号：" label-for="phone" prop="phone">
            <el-input v-model="formItem.phone" type="number" placeholder="负责人电话" />
          </el-form-item>
          <el-form-item label="验证码：" label-for="captcha" prop="captcha">
            <div class="acea-row row-middle code">
              <el-input v-model="formItem.captcha" placeholder="验证码" class="input" />
              <img
                :src="captchs"
                class="pictrue"
                @click="captchsChang"
              >
            </div>
          </el-form-item>
          <el-button type="primary" :loading="loading" class="submit" @click="handleSubmit('formItem')">提交</el-button>
        </el-form>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { authTypeApi, authApplyApi } from '@/api/maintain'
export default {
  name: 'Index',
  data() {
    return {
      formItem: {
        company_name: '',
        domain_name: '',
        order_id: '',
        phone: '',
        captcha: ''
      },
      ruleValidate: {
        company_name: [
          { required: true, message: '请填写您的企业名称', trigger: 'blur' }
        ],
        domain_name: [
          { required: true, message: '请输入域名，格式：baidu.com', trigger: 'blur' }
        ],
        order_id: [
          { required: true, message: '请输入您在淘宝或小程序购买的源码订单号', trigger: 'blur' }
        ],
        phone: [
          { required: true, message: '请输入负责人电话', trigger: 'blur' }
        ],
        captcha: [
          { required: true, message: '请输入验证码', trigger: 'blur' }
        ]
      },
      dialogVisible: false,
      status: 1,
      dayNum: 0,
      captchs: 'http://authorize.crmeb.net/api/captchs/',
      authCode: '',
      loading: false
    }
  },
  mounted() {
    this.getAuth()
    this.captchsChang()
  },
  methods: {
    captchsChang() {
      this.captchs = this.captchs + Date.parse(new Date())
    },
    cancel() {
      this.dialogVisible = false
    },
    // 提交
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.loading = true
          authApplyApi(this.formItem).then(res => {
            this.$message.success(res.message)
            this.loading = false
            this.dialogVisible = false
            this.getAuth()
          }).catch(res => {
            this.loading = false
            this.captchsChang()
            this.$message.error(res.message)
          })
        } else {
          return false
        }
      })
    },
    getAuth() {
      authTypeApi().then(res => {
        const data = res.data || {}
        this.authCode = data.authCode || ''
        this.status = data.status === undefined ? -1 : data.status
        this.dayNum = data.day || 0
      }).catch(({ message }) => {
        this.$message.error(message)
      })
    },
    toCrmeb() {
      window.open('http://www.crmeb.com')
    }
  }
}
</script>

<style scoped>
  .auth {
    padding: 9px 16px 9px 10px;
  }

  .auth .iconIos {
    font-size: 40px;
    margin-right: 10px;
    color: #001529;
  }

  .auth .text {
    font-weight: 400;
    color: rgba(0, 0, 0, 1);
    font-size: 18px;
  }

  .auth .text .code {
    font-size: 14px;
    color: rgba(0, 0, 0, 0.5);
    margin-top: 5px;
  }

  .auth .blue {
    color: #1890FF !important;
  }

  .auth .red {
    color: #ED4014 !important;
  }

  .grey {
    background-color: #999999;
    border-color: #999999;
    color: #fff;
  }

  .submit {
    width: 100%;
  }

  .code .input {
    width: 83%;
  }

  .code .input .ivu-input {
    border-radius: 4px 0 0 4px !important;
  }

  .code .pictrue {
    height: 32px;
    width: 17%;
  }

  .customer {
    border-right: 0;
  }

  .customer a {
    font-size: 12px;
  }

  .ivu-input-group-prepend, .ivu-input-group-append {
    background-color: #fff;
  }

  .ivu-input-group .ivu-input {
    border-right: 0 !important;
  }
</style>
