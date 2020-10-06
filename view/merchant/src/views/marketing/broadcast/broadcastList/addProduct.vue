<template>
  <div class="divBox">
    <el-card class="box-card">
      <form-create
        v-if="FormData"
        ref="fc"
        v-loading="loading"
        :option="option"
        :rule="FormData.rule"
        class="formBox"
        handle-icon="false"
        @on-submit="onSubmit"
        :cmovies="movies"
      />
    </el-card>
  </div>
</template>

<script>
import formCreate from "@form-create/element-ui";
import { createBroadcastProApi } from "@/api/marketing";
import request from "@/api/request";
import { roterPre } from "@/settings";

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
              }
            }
          }
        }
      },
      FormData: null,
      loading: false,
      movies: 1,
    };
  },
  components: {
    formCreate: formCreate.$form(),
  },
  mounted() {
    this.getFrom();
    /* eslint-disable */
  },
  methods: {
    getFrom() {
      this.loading = true;
      sessionStorage.setItem("singleChoice", 1);
      createBroadcastProApi()
        .then(async (res) => {
          this.FormData = res.data;
          this.loading = false;
        })
        .catch((res) => {
          this.$message.error(res.message);
          this.loading = false;
        });
    },
    onSubmit(formData) {
      request[this.FormData.method.toLowerCase()](
        this.FormData.action.slice(5),
        formData
      )
        .then((res) => {
          this.$message.success(res.message || "提交成功");
          this.$router.push({ path: `${roterPre}/marketing/broadcast/list` });
        })
        .catch((err) => {
          this.$message.error(err.message || "提交失败");
        });
    },
  },
};
</script>

<style scoped>
</style>
