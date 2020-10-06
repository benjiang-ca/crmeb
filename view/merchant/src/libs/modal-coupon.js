export default function modalCoupon(couponData, handle, couponId, keyNum, callback) {
  const h = this.$createElement
  return new Promise((resolve, reject) => {
    this.$msgbox({
      title: '优惠券列表',
      customClass: 'upload-form-coupon',
      closeOnClickModal: false,
      showClose: false,
      message: h('div', { class: 'common-form-upload' }, [
        h('couponList', {
          props: {
            couponData: couponData,
            handle: handle,
            couponId: couponId,
            keyNum: keyNum
          },
          on: {
            getCouponId(id) {
              callback(id)
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
