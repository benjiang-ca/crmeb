export function modalSure(title) {
  return new Promise((resolve, reject) => {
    this.$confirm(`确定${title || '删除该文件吗'}?`, '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
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
export function modalSureDelete(title) {
  return new Promise((resolve, reject) => {
    this.$confirm(`${title || '该记录删除后不可恢复，您确认删除吗？'}?`, '提示', {
      confirmButtonText: '删除',
      cancelButtonText: '不删除',
      type: 'warning'
    }).then(() => {
      resolve()
    }).catch(action => {
      this.$message({
        type: 'info',
        message: '已取消'
      })
    })
  })
}
