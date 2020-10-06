<template>
  <div class="divBox">
    <el-card class="box-card">
      <news-category :is-show="isShow" />
    </el-card>
  </div>
</template>

<script>
import newsCategory from '@/components/newsCategory/index'
export default {
  name: 'NewsCategoryIndex',
  components: {
    newsCategory
  },
  data() {
    return {
      isShow: true
    }
  }
}
</script>

<style scoped>

</style>
