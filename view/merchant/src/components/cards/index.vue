<template>
  <div :gutter="10" class="ivu-mt">
    <div
      v-for="(item, index) in cardLists"
      :key="index"
      class="ivu-mb mb10"
    >
      <div class="card_box">
        <div class="card_box_cir" :class="{'one':index%5==0,'two':index%5==1,'three':index%5==2,'four':index%5==3,'five':index%5==4}">
          <div class="card_box_cir1" :class="{'one1':index%5==0,'two1':index%5==1,'three1':index%5==2,'four1':index%5==3,'five1':index%5==4}">
            <i :class="item.className" style="font-size: 24px;"/>
          </div>
        </div>
        <div class="card_box_txt">
          <span class="sp1" v-text="item.count || 0" />
          <span class="sp2" v-text="item.name" />
        </div>
      </div>
    </div>
    <div class="ivu-mb mb10"></div>
  </div>
</template>
<script>
export default {
  name: 'Index',
  props: {
    cardLists: Array
  }
}
</script>

<style scoped lang="scss">
  .ivu-mt{
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
  }
  .ivu-mb{
    width: 19%;
    min-width: 235px;
  }
  .one{
    background: #E4ECFF;
  }
  .two{
    background: #FFF3E0;
  }
  .three{
    background: #EAF9E1;
  }
  .four{
    background: #FFEAF4;
  }
  .five{
    background: #F1E4FF;
  }
  .one1{
    background: #4D7CFE;
  }
  .two1{
    background: #FFAB2B;
  }
  .three1{
    background: #6DD230;
  }
  .four1{
    background: #FF85C0;
  }
  .five1{
    background: #B37FEB;
  }
  .card_box {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    /*justify-content: center*/
    padding: 25px;
    box-sizing: border-box;
    border-radius: 4px;
    background: #f5f7f9;
    .card_box_cir {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      .card_box_cir1 {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
      }
    }
    .card_box_txt {
      .sp1 {
        display: block;
        color: #252631;
        font-size: 24px;
        line-height: 37px;
      }
      .sp2 {
        display: block;
        color: #98A9BC;
        font-size: 12px;
      }
    }
  }
</style>
