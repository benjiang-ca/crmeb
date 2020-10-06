export default function modalTemplates(id, callback, componentKey) {
  const h = this.$createElement
  return new Promise((resolve, reject) => {
    this.$msgbox({
      title: '运费模板',
      customClass: 'upload-form-temp',
      closeOnClickModal: false,
      showClose: false,
      message: h('div', { class: 'common-form-upload' }, [
        h('templatesFrom', {
          props: {
            tempId: id,
            componentKey: componentKey
          },
          on: {
            getList() {
              callback()
            }
          }
        })
      ]),
      showCancelButton: false,
      showConfirmButton: false
    }).then(() => {
      resolve()
    }).catch(() => {
      reject()
      this.$message({
        type: 'info',
        message: '已取消'
      })
    })
  })
}
