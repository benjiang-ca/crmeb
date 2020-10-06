<template>
  <div class="divBox">
    <el-card class="box-card">
      <form-create v-if="FromData" :option="option" :rule="FromData.rule" @on-submit="onSubmit" />
    </el-card>
  </div>
</template>

<script>
import formCreate from '@form-create/element-ui'
import { modifyStoreApi } from '@/api/systemForm'
import request from '@/api/request'

export default {
  name: 'Basics',
  components: { formCreate: formCreate.$form() },
  data() {
    return {
      option: {
        form: {
          labelWidth: '150px'
        },
        global: {
          upload: {
            props: {
              onSuccess(rep, file) {
                if (rep.status === 200) {
                  file.url = rep.data.src
                }
              }
            }
          }
        }
      },
      FromData: null
    }
  },
  mounted() {
    this.getFrom()
  },
  methods: {
    getFrom() {
      modifyStoreApi().then(async res => {
        this.FromData = res.data
      }).catch(res => {
        this.$message.error(res.message)
      })
    },
    onSubmit(formData) {
      request[this.FromData.method.toLowerCase()](this.FromData.action.slice(5), formData).then((res) => {
        this.$message.success(res.message || '提交成功')
      }).catch(err => {
        this.$message.error(err.message || '提交失败')
      })
    }
  }
}
</script>

<style>
  .form-create .el-form-item__label {
    font-size: 12px !important;
  }
</style>
