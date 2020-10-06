<template>
<div>
    <el-row :gutter="30">
        <el-col v-bind="grid">
            <div class="Nav">
                <div class="input">
                    <el-input v-model="filterText" placeholder="选择分类" prefix-icon="el-icon-search" style="width: 100%;" clearable />
                </div>
                <div class="trees-coadd">
                    <div class="scollhide">
                        <div class="trees">
                            <el-tree ref="tree" :data="treeData2" :filter-node-method="filterNode" :props="defaultProps" highlight-current>
                                <div slot-scope="{ node, data}" class="custom-tree-node" @click.stop="handleNodeClick(data)">
                                    <div>
                                        <span>{{ node.label }}</span>
                                        <span v-if="data.space_property_name" style="font-size: 11px;color: #3889b1">（{{ data.attachment_category_name }}）</span>
                                    </div>
                                    <span class="el-ic">
                                        <i class="el-icon-circle-plus-outline" @click.stop="onAdd(data.attachment_category_id)" />
                                        <!--                      <svg-icon-->
                                        <!--                        icon-class="danyuan"-->
                                        <!--                        title="添加管理单元"-->
                                        <!--                        class="icon-space"-->
                                        <!--                      />-->
                                        <i v-if="data.space_id!='0' && (!data.children || data.children == 'undefined') && data.attachment_category_id" class="el-icon-edit" title="修改" @click.stop="onEdit(data.attachment_category_id)" />
                                        <!--                      <svg-icon-->
                                        <!--                        icon-class="detail"-->
                                        <!--                        title="查看该空间详情"-->
                                        <!--                        class="icon-space"-->
                                        <!--                      />-->
                                        <i v-if="data.space_id!='0' && (!data.children || data.children == 'undefined') && data.attachment_category_id" class="el-icon-delete" title="删除分类" @click.stop="() => handleDelete(data.attachment_category_id)" />
                                    </span>
                                </div>
                            </el-tree>
                        </div>
                    </div>
                </div>
            </div>
        </el-col>
        <el-col v-bind="grid2" class="colLeft">
            <div v-loading="loading" class="conter">
                <div class="bnt">
                    <el-button v-if="params !== '/admin/config/picture'" size="mini" type="primary" class="mb10 mr10" @click="checkPics">使用选中图片</el-button>
                    <el-upload class="upload-demo mr10 mb15" :action="fileUrl" :on-success="handleSuccess" :headers="myHeaders" :show-file-list="false" multiple>
                        <el-button size="mini" type="primary">点击上传</el-button>
                    </el-upload>
                    <el-button type="success" size="mini" @click.stop="onAdd(0)">添加分类</el-button>
                    <el-button type="error" size="mini" class="mr10" :disabled="checkPicList.length===0" @click.stop="editPicList('图片')">删除图片
                    </el-button>
                    <el-select v-model="sleOptions.attachment_category_name" placeholder="图片移动至" class="mb15" size="mini">
                        <el-option :label="sleOptions.attachment_category_name" :value="sleOptions.attachment_category_id" style="max-width: 560px;height:200px;overflow: auto;background-color:#fff">
                            <el-tree ref="tree2" :data="treeData2" :filter-node-method="filterNode" :props="defaultProps" highlight-current @node-click="handleSelClick" />
                        </el-option>
                    </el-select>
                </div>
                <div class="pictrueList acea-row">
                    <div v-show="isShowPic" class="imagesNo">
                        <i class="el-icon-picture" style="font-size: 60px;color: rgb(219, 219, 219);" />
                        <span class="imagesNo_sp">图片库为空</span>
                    </div>
                    <div class="conters">
                        <div v-for="(item, index) in pictrueList.list" :key="index" class="gridPic">
                            <img v-lazy="item.attachment_src" :class="item.isSelect ? 'on': '' " @click="changImage(item, index, pictrueList.list)">
                        </div>
                    </div>
                </div>
                <div class="block">
                    <el-pagination :page-sizes="[12, 20, 40, 60]" :page-size="tableData.limit" :current-page="tableData.page" layout="total, sizes, prev, pager, next, jumper" :total="pictrueList.total" @size-change="handleSizeChange" @current-change="pageChange" />
                </div>
            </div>
        </el-col>
    </el-row>
</div>
</template>

<script>
import {
    formatLstApi,
    attachmentCreateApi,
    attachmentUpdateApi,
    attachmentDeleteApi,
    attachmentListApi,
    picDeleteApi,
    categoryApi,
} from '@/api/system'
import {
    getToken
} from '@/utils/auth'
import SettingMer from '@/libs/settingMer'
export default {
    name: 'Upload',
    props: {
        isMore: {
            type: String,
            default: '1',
        },
    },
    data() {
        return {
            loading: false,
            params: '',
            sleOptions: {
                attachment_category_name: '',
                attachment_category_id: '',
            },
            list: [],
            grid: {
                xl: 8,
                lg: 8,
                md: 8,
                sm: 8,
                xs: 24,
            },
            grid2: {
                xl: 16,
                lg: 16,
                md: 16,
                sm: 16,
                xs: 24,
            },
            filterText: '',
            treeData: [],
            treeData2: [],
            defaultProps: {
                children: 'children',
                label: 'attachment_category_name',
            },
            classifyId: 0,
            myHeaders: {
                'X-Token': getToken(),
            },
            tableData: {
                page: 1,
                limit: 12,
                attachment_category_id: 0,
            },
            pictrueList: {
                list: [],
                total: 0,
            },
            isShowPic: false,
            checkPicList: [],
            ids: [],
            checkedMore: [],
            checkedAll: [],
        }
    },
    computed: {
        fileUrl() {
            return (
                SettingMer.https +
                `/upload/image/${this.tableData.attachment_category_id}/file`
            )
        },
    },
    watch: {
        filterText(val) {
            this.$refs.tree.filter(val)
        },
    },
    mounted() {
        this.params = this.$route && this.$route.path ? this.$route.path : ''
        if (this.$route && this.$route.query.field === 'dialog')
            import('../../../public/UEditor/dialogs/internal')
        this.getList()
        this.getFileList()
    },
    methods: {
        // 搜索分类
        filterNode(value, data) {
            if (!value) return true
            return data.attachment_category_name.indexOf(value) !== -1
        },
        // 所有分类
        getList() {
            const data = {
                attachment_category_name: '全部图片',
              attachment_category_id: 0,
            }
            formatLstApi()
                .then((res) => {
                    this.treeData = res.data
                    this.treeData.unshift(data)
                    this.treeData2 = [...this.treeData]
                })
                .catch((res) => {
                    this.$message.error(res.message)
                })
        },
        // 添加分类
        onAdd(id) {
            const config = {}
            if (Number(id) > 0)
                config.formData = {
                    pid: id,
                }
            this.$modalForm(attachmentCreateApi(), config).then(({
                message
            }) => {
                // this.$message.success(message)
                this.getList()
            })
        },
        // 编辑
        onEdit(id) {
            this.$modalForm(attachmentUpdateApi(id)).then(() => this.getList())
        },
        // 删除
        handleDelete(id) {
            this.$modalSure().then(() => {
                attachmentDeleteApi(id)
                    .then(({
                        message
                    }) => {
                        this.$message.success(message)
                        this.getList()
                    })
                    .catch(({
                        message
                    }) => {
                        this.$message.error(message)
                    })
            })
        },
        handleNodeClick(data) {
            this.tableData.attachment_category_id = data.attachment_category_id
            this.getFileList()
        },
        // 上传成功
        handleSuccess(response) {
            if (response.status === 200) {
                this.$message.success('上传成功')
                this.getFileList()
            } else {
                this.$message.error(response.message)
            }
        },
        // 文件列表
        getFileList() {
            this.loading = true
            attachmentListApi(this.tableData)
                .then(async (res) => {
                    this.pictrueList.list = res.data.list
                    if (this.pictrueList.list.length) {
                        this.isShowPic = false
                    } else {
                        this.isShowPic = true
                    }
                    this.pictrueList.total = res.data.count
                    if (
                        this.$route &&
                        this.$route.query.field &&
                        this.$route.query.field !== 'dialog'
                    )
                        this.checkedMore =
                        window.form_create_helper.get(this.$route.query.field) || []
                    this.loading = false
                })
                .catch((res) => {
                    this.$message.error(res.message)
                    this.loading = false
                })
        },
        pageChange(page) {
            this.tableData.page = page
            this.getFileList()
        },
        handleSizeChange(val) {
            this.tableData.limit = val
            this.getFileList()
        },
        // 选中图片
        changImage(item, index, row) {
            let selectItem = ''
            this.$set(
                item,
                'isSelect',
                item.isSelect === undefined ? true : !item.isSelect
            )
            selectItem = this.pictrueList.list.filter((item) => {
                return item.isSelect === true
            })
            this.ids = []
            const pic = []
            selectItem.map((item, index) => {
                this.ids.push(item.attachment_id)
                pic.push(item.attachment_src)
            })
            this.checkPicList = pic
        },
        // 点击使用选中图片
        checkPics() {
            if (this.checkPicList.length) {
                if (this.$route) {
                    if (this.$route.query.type === '1') {
                        if (this.checkPicList.length > 1)
                            return this.$message.warning('最多只能选一张图片')
                        /* eslint-disable */
                        form_create_helper.set(
                            this.$route.query.field,
                            this.checkPicList[0]
                        )
                        form_create_helper.close(this.$route.query.field)
                    }
                    if (this.$route.query.type === '2') {
                        this.checkedAll = [...this.checkedMore, ...this.checkPicList]
                        form_create_helper.set(
                            this.$route.query.field,
                            Array.from(new Set(this.checkedAll))
                        )
                        form_create_helper.close(this.$route.query.field)
                    }
                    if (this.$route.query.field === 'dialog') {
                        let str = ''
                        for (let i = 0; i < this.checkPicList.length; i++) {
                            str += '<img src="' + this.checkPicList[i] + '">'
                        }
                        /* eslint-disable */
                        nowEditor.dialog.close(true)
                        nowEditor.editor.setContent(str, true)
                    }
                } else {
                    if (this.isMore === '1' && this.checkPicList.length > 1) {
                        return this.$message.warning('最多只能选一张图片')
                    }
                    this.$emit('getImage', this.checkPicList)
                }
            } else {
                this.$message.warning('请先选择图片')
            }
        },
        // 删除图片
        editPicList(tit) {
            const ids = {
                ids: this.ids,
            }
            this.$modalSure().then(() => {
                picDeleteApi(ids)
                    .then(({
                        message
                    }) => {
                        this.$message.success(message)
                        this.getFileList()
                        this.checkPicList = []
                    })
                    .catch(({
                        message
                    }) => {
                        this.$message.error(message)
                    })
            })
        },
        // 移动分类点击
        handleSelClick(node) {
            if (this.ids.length) {
                this.sleOptions = {
                    attachment_category_name: node.attachment_category_name,
                    attachment_category_id: node.attachment_category_id,
                }
                this.getMove()
            } else {
                this.$message.warning('请先选择图片')
            }
        },
        getMove() {
            categoryApi(this.ids, this.sleOptions.attachment_category_id)
                .then(async (res) => {
                    this.$message.success(res.message)
                    this.clearBoth()
                    this.getFileList()
                })
                .catch((res) => {
                    this.clearBoth()
                    this.$message.error(res.message)
                })
        },
        clearBoth() {
            this.sleOptions = {
                attachment_category_name: '',
                attachment_category_id: '',
            }
            this.checkPicList = []
            this.ids = []
        },
    },
}
</script>

<style lang="scss" scoped>
/deep/ .el-pagination__jump {
    margin-left: 0;
}

.selectTreeClass {
    background: #d5e8fc;
}

.treeBox {
    width: 100%;
    height: 100%;
}

.upload-demo {
    display: inline-block !important;
}

.tree_w {
    padding: 20px 30px;
}

.custom-tree-node {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    padding-right: 8px;
    color: #4386c6;

    div {
        span {
            width: 100px;
            display: inline-block;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }
}

.el-ic {
    display: none;

    i,
    span {
        /*padding: 0 14px;*/
        font-size: 18px;
        font-weight: 600;
    }

    .svg-icon {
        color: #4386c6;
    }
}

.el-tree-node__content {
    height: 38px;
}

.el-tree-node__expand-icon {
    color: #428bca;
    /*padding: 10px 10px 0px 10px !important;*/
}

.el-tree-node__content:hover .el-ic {
    color: #428bca !important;
    display: inline-block;
}

.el-tree-node__content:hover {
    font-weight: bold;
}

.el-tree--highlight-current .el-tree-node.is-current>.el-tree-node__content :hover {
    .el-tree-node__expand-icon.is-leaf {
        color: transparent;
        cursor: default;
    }

    /*background-color: #3998d9;*/
    .custom-tree-node {
        font-weight: bold;
    }

    .el-tree-node__expand-icon {
        font-weight: bold;
    }
}

.el-dialog__body {
    .upload-container .image-preview .image-preview-wrapper img {
        height: 100px;
    }

    .el-dialog .el-collapse-item__wrap {
        padding-top: 0px;
    }

    .spatial_img {
        .el-collapse-item__wrap {
            margin-bottom: 0;
            padding-top: 0px;
        }
    }

    .upload-container .image-preview .image-preview-wrapper {
        width: 120px;
    }

    .upload-container .image-preview .image-preview-action {
        line-height: 100px;
        height: 100px;
    }
}

.trees-coadd {
    width: 100%;
    border-radius: 4px;
    overflow: hidden;
    position: relative;

    .scollhide {
        overflow-x: hidden;
        overflow-y: scroll;
        padding: 10px 0 10px 0;
        box-sizing: border-box;

        .trees {
            width: 100%;
            max-height: 374px;
        }
    }

    .scollhide::-webkit-scrollbar {
        display: none;
    }
}

.conters {
    display: flex;
    flex-wrap: wrap;
}

.gridPic {
    margin-right: 20px;
    margin-bottom: 10px;
    width: 110px;
    height: 110px;
    cursor: pointer;

    img {
        width: 100%;
        height: 100%;
        display: block;
    }
}

.conter {
    width: 99%;
    height: 100%;

    .bnt {
        width: 100%;
        padding: 0 13px 10px 15px;
        box-sizing: border-box;
    }

    .pictrueList {
        padding-left: 15px;
        width: 100%;

        el-image {
            width: 100%;
            border: 2px solid #fff;
        }

        .on {
            border: 2px solid #5fb878;
        }
    }

    .el-image {
        width: 110px;
        height: 110px;
        cursor: pointer;
    }

    .imagesNo {
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
        margin: 65px auto;

        .imagesNo_sp {
            font-size: 13px;
            color: #dbdbdb;
            line-height: 3;
        }
    }
}
</style>
