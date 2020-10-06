<template>
  <div>
    <el-dialog
      title="提示"
      :visible.sync="visible"
      width="896px"
      :before-close="handleClose"
    >
      <upload-index v-if="visible" @getImage="getImage"/>
      <!--<span slot="footer" class="dialog-footer" />-->
    </el-dialog>
  </div>
</template>

<script>
import UploadIndex from '@/components/uploadPicture/index.vue'
export default {
  name: 'UploadFroms',
  components: { UploadIndex },
  data() {
    return {
      visible: false,
      callback: function() {}
    }
  },
  watch: {
    // show() {
    //   this.visible = this.show
    // }
  },
  methods: {
    handleClose() {
      this.visible = false
    },
    getImage(img) {
      this.callback(img)
      this.visible = false
    }
  }
}
</script>

<style scoped>

</style>
