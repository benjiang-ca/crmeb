<template>
  <div class="divBox">
    <el-card class="box-card">
      <el-form ref="promoterForm" :model="promoterForm" :rules="rules" label-width="200px" class="demo-promoterForm">
        <el-form-item prop="extension_status">
          <span slot="label">
            <span>分销启用：</span>
            <el-tooltip class="item" effect="dark" content="商城分销功能开启关闭" placement="top-start">
              <i class="el-icon-warning-outline" />
            </el-tooltip>
          </span>
          <el-radio-group v-model="promoterForm.extension_status">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="0">关闭</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item prop="extension_one_rate">
          <span slot="label">
            <span>一级返佣比例：</span>
            <el-tooltip class="item" effect="dark" content="订单交易成功后给上级返佣的比例0 - 100,例:5 = 反订单金额的5%" placement="top-start">
              <i class="el-icon-warning-outline" />
            </el-tooltip>
          </span>
          <el-input-number v-model="promoterForm.extension_one_rate" :precision="2" :step="0.1" :min="0" class="selWidth"></el-input-number>
          <span>%</span>
        </el-form-item>
        <el-form-item prop="extension_two_rate">
          <span slot="label">
            <span>二级返佣比例：</span>
            <el-tooltip class="item" effect="dark" content="订单交易成功后给上级返佣的比例0 ~ 100,例:5 = 反订单金额的5%" placement="top-start">
              <i class="el-icon-warning-outline" />
            </el-tooltip>
          </span>
          <el-input-number v-model="promoterForm.extension_two_rate" :precision="2" :step="0.1" :min="0" class="selWidth"></el-input-number>
          <span>%</span>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="loading" @click="submitForm('promoterForm')">立即创建</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { configApi, configUpdateApi, productCheckApi } from '@/api/promoter'
export default {
  name: 'Index',
  data() {
    return {
      promoterForm: {},
      loading: false,
      rules: {
        extension_status: [
          { required: true, message: '请选择是否启用分销', trigger: 'change' }
        ],
        extension_one_rate: [
          { required: true, message: '请输入一级返佣比例', trigger: 'blur' }
        ],
        extension_two_rate: [
          { required: true, message: '请输入二级返佣比例', trigger: 'blur' }
        ]
      }
    }
  },
  mounted() {
    this.getDetal()
  },
  methods: {
    getDetal() {
      configApi().then(res => {
        this.promoterForm = res.data
        this.promoterForm.extension_status = Number(res.data.extension_status)
      }).catch((res) => {
        this.$message.error(res.message)
      })
    },
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.loading = true
          configUpdateApi(this.promoterForm).then(res => {
            this.loading = false
            this.$modalSure('提交成功，是否自动下架商户低于此佣金比例的商品').then(() => {
              productCheckApi().then(({ message }) => {
                this.$message.success(message)
              }).catch(({ message }) => {
                this.$message.error(message)
              })
            })
          }).catch((res) => {
            this.$message.error(res.message)
            this.loading = false
          })
        } else {
          return false
        }
      })
    }
  }
}
</script>

<style scoped>
  .selWidth{
    width: 300px;
  }
</style>
