export default function modalUpload() {
  const h = this.$createElement
  return new Promise((resolve, reject) => {
    this.$msgbox({
      title: '上传图片',
      customClass: 'upload-form',
      message: h('div', { class: 'common-form-upload' }, [
        h('uploadFrom', {
          // props: {
          //   rule: data.rule,
          //   option: data.config
          // },
          // on: {
          //   mounted: ($f) => {
          //     fApi = $f
          //   }
          // }
        })
      ]),
      showCancelButton: true,
      confirmButtonText: '确定',
      cancelButtonText: '取消'
    }).then(() => {
      resolve()
    }).catch(() => {
      this.$message({
        type: 'info',
        message: '已取消'
      })
    })
  })
}
