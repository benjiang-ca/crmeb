<template>
  <div class="divBox">
    <el-card class="box-card">
      <upload-from />
    </el-card>
  </div>
</template>

<script>
import uploadFrom from '@/components/uploadPicture/index.vue'
export default {
  name: 'Picture',
  components: { uploadFrom },
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
