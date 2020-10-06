<template>
  <div class="divBox">
    <el-card class="box-card FromData">
      <form-create v-if="FromData" :option="option" :rule="FromData.rule" @on-submit="onSubmit" />
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import formCreate from '@form-create/element-ui'
import { configApi } from '@/api/systemForm'
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
      FromData: null,
      titles: ''
    }
  },
  mounted() {
    this.setTagsViewTitle()
    this.getFrom()
  },
  created() {
    this.tempRoute = Object.assign({}, this.$route)
  },
  computed: {
    ...mapGetters(['menuList'])
  },
  methods: {
    getFrom() {
      configApi(this.$route.params.key).then(async res => {
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
    },
    setTagsViewTitle() {
      this.deepTraversal(this.menuList, 'children')
      const route = Object.assign({}, this.tempRoute, { title: this.titles })
      this.$store.dispatch('tagsView/updateVisitedView', route)
    },
    deepTraversal(arr, child) {
      const that = this

      function traversal(a) {
        a.forEach(o => {
          if (o.route.indexOf('Basics') !== -1 && o.route === that.$route.path) {
            that.titles = o.menu_name
            return
          }
          if (o[child] && o[child].length) {
            traversal(o[child])
          }
        })
      }

      traversal(arr)
    }

  }
}
</script>

<style scoped lang="scss">
  .form-create .el-form-item__label {
    font-size: 12px !important;
  }

  .FromData {
    /deep/ .el-textarea__inner {
      min-height: 100px !important;
    }
  }
</style>
