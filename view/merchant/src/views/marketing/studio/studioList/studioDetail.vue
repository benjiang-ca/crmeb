<template>
  <div class="divBox">
    <el-dialog title="直播间信息" :visible.sync="dialogVisible" width="700px" v-if="dialogVisible">
      <div v-loading="loading">
        <div class="box-container">
          <div class="list sp">
            <label class="name">直播间名称：</label>
            <span class="info">{{ FormData.name }}</span>
          </div>
          <div class="list sp">
            <label class="name">主播微信号：</label>
            <span class="info">{{ FormData.anchor_wechat }}</span>
          </div>
          <div class="list sp">
            <label class="name">直播间ID：</label>
            <span class="info">{{ FormData.broadcast_room_id }}</span>
          </div>
          <div class="list sp">
            <label class="name">主播昵称：</label>
            <span class="info">{{ FormData.anchor_name }}</span>
          </div>
          <div class="list sp">
            <label class="name">手机号：</label>
            <span class="info">{{ FormData.phone }}</span>
          </div>
          <div class="list sp">
            <label class="name">审核结果：</label>
            <span class="info">{{ FormData.status | liveReviewStatusFilter }}</span>
          </div>
          <div class="list sp">
            <label class="name">直播开始时间：</label>
            <span class="info">{{ FormData.start_time }}</span>
          </div>
          <div class="list sp">
            <label class="name">直播结束时间：</label>
            <span class="info">{{ FormData.end_time }}</span>
          </div>
          <div class="list sp">
            <label class="name">直播间类型：</label>
            <span class="info">{{ FormData.type | broadcastType }}</span>
          </div>
          <div class="list sp">
            <label class="name">显示类型：</label>
            <span class="info">{{ FormData.screen_type | broadcastDisplayType }}</span>
          </div>
          <div class="list sp image">
            <label class="name">背景图：</label>
            <img style="max-width: 150px; height: 80px;" :src="FormData.cover_img" />
          </div>
          <div class="list sp image">
            <label class="name">分享图：</label>
            <img style="max-width: 150px; height: 80px;" :src="FormData.share_img" />
          </div>
          <div class="list sp">
            <label class="name">是否开启点赞：</label>
            <span class="info blue">{{ FormData.close_like | filterClose }}</span>
          </div>
          <div class="list sp">
            <label class="name">是否开启货架：</label>
            <span class="info blue">{{ FormData.close_goods | filterClose }}</span>
          </div>
          <div class="list sp">
            <label class="name">是否开启评论：</label>
            <span class="info blue">{{ FormData.close_comment | filterClose }}</span>
          </div>

          <div class="list sp">
            <label class="name">是否开启直播回放：</label>
            <span class="info blue">{{ FormData.replay_status ? "✔" : "✖" }}</span>
          </div>
          <div class="list sp">
            <label class="name">是否开启分享：</label>
            <span class="info blue">{{ FormData.close_share ? "✖" : "✔" }}</span>
          </div>
          <div class="list sp">
            <label class="name">是否开启客服：</label>
            <span class="info blue">{{ FormData.close_kf ? "✖" : "✔" }}</span>
          </div>
          <div v-if="FormData.status === 2" class="list sp100">
            <label class="name">已导入直播商品：</label>
            <selected-goods ref="selectedGoods" v-bind:broadcast_room_id="FormData.broadcast_room_id"/>
          </div>
          <div class="list sp100">
            <label class="name">备注：</label>
            <span class="info">
              <el-input v-model="FormData.mark" type="textarea" :rows="1" />
            </span>
          </div>
          <div class="list sp100 mt20">
            <el-button
              type="button"
              class="el-button el-button--primary el-button--medium"
              style="width: 100%;"
              :disabled="FormData.mark == ''"
              @click="handleRemarks()"
            >提交</el-button>
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { broadcastDetailApi, broadcastRemarksApi } from "@/api/marketing";
import selectedGoods from './selectedGoods'

export default {
  name: "studioDetail",
  components: {
    selectedGoods
  },
  data() {
    return {
      dialogVisible: false,
      option: {
        form: {
          labelWidth: "150px",
        },
      },
      FormData: {},
      loading: false,
    };
  },
  mounted() {

  },
  methods: {
    getData(id) {
      this.loading = true;
      broadcastDetailApi(id)
        .then((res) => {
          this.FormData = res.data;
          this.loading = false;
          console.log(this.FormData);
        })
        .catch((res) => {
          this.$message.error(res.message);
          this.loading = false;
        });
    },
    handleRemarks(id) {
      broadcastRemarksApi(this.FormData.broadcast_room_id, this.FormData.mark)
        .then((res) => {
          this.loading = false;
          this.$message.success(res.message);
          this.dialogVisible = false;
          this.$emit("getList");
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
}
.box-container .list {
  float: left;
  line-height: 40px;
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
  width: 150px;
  text-align: right;
  color: #606266;
}
.box-container .list .blue {
  color: #1890ff;
}
.box-container .list.image {
  margin-bottom: 40px;
}
.box-container .list.image img {
  position: relative;
  top: 40px;
}
.el-textarea {
  width: 400px;
}
</style>
