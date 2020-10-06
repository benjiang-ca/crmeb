export default function modalNewsCategory() {
  const h = this.$createElement
  return new Promise((resolve, reject) => {
    this.$msgbox({
      title: '发送消息',
      customClass: 'upload-form',
      message: h('div', { class: 'common-form-upload' }, [
        h('newsCategory', {
          on: {
            getImageId(id) {
            }
          }
        })
      ]),
      showCancelButton: true,
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      beforeClose: (action, instance, done) => {
        if (action === 'confirm') {
          instance.confirmButtonLoading = true
          instance.confirmButtonText = '执行中...'
          setTimeout(() => {
            done()
            setTimeout(() => {
              instance.confirmButtonLoading = false
            }, 300)
          }, 3000)
        } else {
          done()
        }
      }
    }).then(() => {
      resolve()
    }).catch(() => {
      this.$message({
        type: 'info',
        message: '已取消'
      })
    })

    // this.$confirm(h('div', { class: 'common-form-upload' }, [
    //   h('uploadFrom', {})]), {
    //   customClass: 'upload-form',
    //   confirmButtonText: '确定',
    //   cancelButtonText: '取消'
    // }).then((res) => {
    // }).catch(() => {
    //   this.$message({
    //     type: 'info',
    //     message: '已取消'
    //   })
    // })
  })
}
