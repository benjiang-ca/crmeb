<template>
  <div class="divBox">
    <el-card class="box-card">
      <div class="box-container">
        <div class="list sp">
          <label class="name">直播间名称：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp">
          <label class="name">主播微信号：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp3">
          <label class="name">直播间ID：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp3">
          <label class="name">主播昵称：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp3">
          <label class="name">手机号：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp">
          <label class="name">直播间类型：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp">
          <label class="name">显示类型：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp">
          <label class="name">背景图：</label>
          <img :src="FormData.src" alt />
        </div>
        <div class="list sp">
          <label class="name">分享图：</label>
          <img :src="FormData.src" alt />
        </div>
        <div class="list sp100">
          <label class="name">直播时间：</label>
          <span class="info">{{ }}</span>
        </div>
        <div class="list sp100">
          <label class="name">是否关闭点赞：</label>
          <span class="info" v-if="FormData.like">{{ }}</span>
          <span class="info" v-else>{{ }}</span>
        </div>
        <div class="list sp100">
          <label class="name">是否关闭货架：</label>
          <span class="info" v-if="FormData.like">{{ }}</span>
          <span class="info" v-else>{{ }}</span>
        </div>
        <div class="list sp100">
          <label class="name">是否关闭评论：</label>
          <span class="info" v-if="FormData.like">{{ }}</span>
          <span class="info" v-else>{{ }}</span>
        </div>
        <div class="list sp100">
          <label class="name">审核结果</label>
          <span class="info" v-if="FormData.audit">{{ }}</span>
          <span class="info" v-else>{{ }}</span>
        </div>
        <div class="list sp100">
          <label class="name">是否显示直播回放：</label>
          <span class="info" v-if="FormData.aduit">{{ }}</span>
          <span class="info" v-else>{{ }}</span>
        </div>
        <div class="list sp100">
          <label class="name">备注：</label>
          <span class="info">{{ }}</span>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
import { broadcastDetailApi } from "@/api/marketing";

export default {
  name: "CreatCoupon",
  data() {
    return {
      option: {
        form: {
          labelWidth: "150px",
        },
        global: {
          upload: {
            props: {
              onSuccess(rep, file) {
                if (rep.status === 200) {
                  file.url = rep.data.src;
                }
              },
            },
          },
        },
      },
      FormData: {},
      loading: false,
    };
  },
  components: {
    // formCreate: formCreate.$form()
  },
  mounted() {
    this.getData();
  },
  methods: {
    getData() {
      broadcastDetailApi(this.$route.params.id)
        .then(async (res) => {
          this.FormData = res.data;
          this.loading = false;
        })
        .catch((res) => {
          this.$message.error(res.message);
          this.loading = false;
        });
    },
  },
};
</script>

<style scoped>
.box-container {
  overflow: hidden;
  width: 1200px;
}
.box-container .list {
  float: left;
  line-height: 60px;
}
.box-container .sp {
  width: 50%;
}
.box-container .sp3 {
  width: 33.3333%;
}
.box-container .sp100 {
  width: 100%;
}
.box-container .list .name {
  display: inline-block;
  width: 200px;
  text-align: right;
  color: #606266;
}
</style>
