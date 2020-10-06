<template>
  <div class="divBox">
    <el-card class="box-card">
      <upload-index></upload-index>
    </el-card>
  </div>
</template>

<script>
import UploadIndex from '@/components/uploadPicture/index.vue'
export default {
  name: 'Picture',
  components: { UploadIndex },
  data() {
    return {
    }
  },
  methods: {
  }

}
</script>

<style scoped>

</style>
