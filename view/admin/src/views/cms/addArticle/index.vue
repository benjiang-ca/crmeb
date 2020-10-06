<template>
  <div class="divBox">
    <el-card class="box-card">
      <el-button icon="el-icon-arrow-left" size="mini" class="pan-back-btn mb20" @click="back">返回</el-button>
      <el-form ref="formValidate" class="form" :model="formValidate" label-width="120px" :rules="ruleValidate" @submit.native.prevent>
        <div class="dividerTitle">
          <span class="title mr10">文章信息</span>
          <el-divider />
        </div>
        <el-row :gutter="10">
          <el-col v-bind="grid">
            <el-form-item label="标题：" prop="title" label-for="title">
              <el-input v-model="formValidate.title" placeholder="请输入" element-id="title" style="width: 90%" />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid" class="mr50">
            <el-form-item label="作者：" prop="author" label-for="author">
              <el-input v-model="formValidate.author" placeholder="请输入" maxLength="32" element-id="author" style="width: 90%" />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid">
            <el-form-item label="文章分类：" label-for="cid" prop="cid">
              <el-select v-model="formValidate.cid" clearable placeholder="请选择" class="mb15" style="width: 90%">
                <el-option
                  :label="sleOptions.title"
                  :value="sleOptions.article_category_id"
                  style="width: 560px;height:200px;overflow: auto;background-color:#fff"
                >
                  <el-tree
                    ref="tree2"
                    :data="treeData"
                    :props="defaultProps"
                    highlight-current
                    @node-click="handleSelClick"
                  />
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid" class="mr50">
            <el-form-item label="文章简介：" prop="synopsis" label-for="synopsis">
              <el-input v-model="formValidate.synopsis" type="textarea" placeholder="请输入" style="width: 90%" />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid" class="mr50">
            <el-form-item label="图文封面：" prop="image_input">
              <div class="upLoadPicBox" @click="modalPicTap('1')">
                <div v-if="formValidate.image_input" class="pictrue"><img :src="formValidate.image_input"></div>
                <div v-else class="upLoad">
                  <i class="el-icon-camera cameraIconfont" />
                </div>
              </div>
            </el-form-item>
          </el-col>
        </el-row>
        <div class="dividerTitle">
          <span class="title">文章内容</span>
          <el-divider />
        </div>
        <el-form-item label="文章内容：" prop="content">
          <ueditor-from v-model="formValidate.content" :content="formValidate.content" />
          <!--<vue-ueditor-wrap v-model="formValidate.content" :config="myConfig" style="width: 90%;" @beforeInit="addCustomDialog" />-->
        </el-form-item>
        <div class="dividerTitle">
          <span class="title">其他设置</span>
          <el-divider />
        </div>
        <el-row>
<!--          <el-col :span="24">-->
<!--            <el-form-item label="原文链接：">-->
<!--              <el-input v-model="formValidate.url" placeholder="请输入" style="width: 60%" />-->
<!--            </el-form-item>-->
<!--          </el-col>-->
<!--          <el-col :span="24">-->
<!--            <el-form-item label="banner显示：">-->
<!--              <el-radio-group v-model="formValidate.is_banner">-->
<!--                <el-radio :label="1" class="radio">显示</el-radio>-->
<!--                <el-radio :label="0">不显示</el-radio>-->
<!--              </el-radio-group>-->
<!--            </el-form-item>-->
<!--          </el-col>
          <el-col :span="24">
            <el-form-item label="热门文章：">
              <el-radio-group v-model="formValidate.is_hot">
                <el-radio :label="1" class="radio">显示</el-radio>
                <el-radio :label="0">不显示</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>-->
          <el-col :span="24">
            <el-form-item label="是否显示：">
              <el-radio-group v-model="formValidate.status">
                <el-radio :label="1" class="radio">显示</el-radio>
                <el-radio :label="0">不显示</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
        <el-button type="primary" class="submission" @click="onsubmit('formValidate')">提交</el-button>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { articleListApi, articleAddApi, articleDetailApi, articleEditApi } from '@/api/cms'
import ueditorFrom from '@/components/ueditorFrom'
import { roterPre } from '@/settings'
export default {
  name: 'EditArticle',
  components: { ueditorFrom },
  data() {
    const validateUpload = (rule, value, callback) => {
      if (this.formValidate.image_input) {
        callback()
      } else {
        callback(new Error('请上传图文封面'))
      }
    }
    const validateUpload2 = (rule, value, callback) => {
      if (!this.formValidate.cid) {
        callback(new Error('请选择文章分类'))
      } else {
        callback()
      }
    }
    return {
      roterPre: roterPre,
      sleOptions: {
        title: '',
        article_category_id: ''
      },
      defaultProps: {
        children: 'children',
        label: 'title'
      },
      list: [],
      treeData: [],
      grid: {
        xl: 10,
        lg: 10,
        md: 10,
        sm: 24,
        xs: 24
      },
      formValidate: {
        cid: '',
        title: '',
        author: '',
        image_input: '',
        content: '',
        synopsis: '',
        url: '',
        is_hot: 0,
        is_banner: 0,
        status: 0
      },
      ruleValidate: {
        title: [
          { required: true, message: '请输入标题', trigger: 'blur' }
        ],
        author: [
          { required: true, message: '请输入作者', trigger: 'blur' }
        ],
        cid: [
          { required: true, validator: validateUpload2, trigger: 'change' }
        ],
        image_input: [
          { required: true, validator: validateUpload, trigger: 'change' }
        ],
        content: [
          { required: true, message: '请输入文章内容', trigger: 'change' }
        ]
      },
      tempRoute: {}
    }
  },
  watch: {
    $route(to, from) {
      if (this.$route.params.id) {
        this.getDetails()
      } else {
        this.formValidate = {
          title: '',
          author: '',
          image_input: '',
          content: '',
          synopsis: '',
          url: '',
          is_hot: 0,
          is_banner: 0,
          status: 0
        }
      }
    }
  },
  created() {
    this.tempRoute = Object.assign({}, this.$route)
  },
  mounted() {
    this.getList()
    if (this.$route.params.id) {
      this.setTagsViewTitle()
      this.getDetails()
    }
  },
  methods: {
    // 返回
    back() {
      this.$router.push({ path: `${roterPre}/cms/article` })
    },
    // 所有分类
    getList() {
      articleListApi().then(res => {
        this.treeData = res.data
      }).catch(res => {
        this.$message.error(res.message)
      })
    },
    modalPicTap(tit) {
      const _this = this
      this.$modalUpload(function(img) {
        _this.formValidate.image_input = img[0]
      }, tit)
    },
    // 分类点击
    handleSelClick(node) {
      this.formValidate.cid = node.article_category_id
      this.sleOptions = {
        title: node.title,
        article_category_id: node.article_category_id
      }
    },
    // 提交数据
    onsubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          if (this.$route.params.id) {
            articleEditApi(this.formValidate, this.$route.params.id).then(async res => {
              this.$message.success(res.message)
              setTimeout(() => {
                this.$router.push({ path: `${roterPre}/cms/article` })
              }, 500)
            }).catch(res => {
              this.$message.error(res.message)
            })
          } else {
            articleAddApi(this.formValidate).then(async res => {
              this.$message.success(res.message)
              setTimeout(() => {
                this.$router.push({ path: `${roterPre}/cms/article` })
              }, 500)
            }).catch(res => {
              this.$message.error(res.message)
            })
          }
        } else {
          return false
        }
      })
    },
    // 文章详情
    getDetails() {
      articleDetailApi(this.$route.params.id ? this.$route.params.id : 0).then(async res => {
        const news = res.data
        this.sleOptions.title = news.articleCategory.title
        this.sleOptions.article_category_id = news.articleCategory.article_category_id
        this.formValidate = {
          cid: news.articleCategory.article_category_id,
          title: news.title,
          author: news.author,
          image_input: news.image_input,
          content: news.content.content,
          synopsis: news.synopsis,
          url: news.url,
          is_hot: news.is_hot,
          is_banner: news.is_banner,
          status: news.status
        }
      }).catch(res => {
        this.loading = false
        this.$message.error(res.message)
      })
    },
    setTagsViewTitle() {
      const title = '编辑文章'
      const route = Object.assign({}, this.tempRoute, { title: `${title}-${this.$route.params.id}` })
      this.$store.dispatch('tagsView/updateVisitedView', route)
    }
  }
}
</script>

<style scoped lang="scss">
/deep/ .el-pagination__jump{
  margin-left: 0;
}
</style>
