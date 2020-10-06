<template>
  <div class="divBox">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <el-steps :active="currentTab" align-center finish-status="success">
          <el-step title="商品信息" />
          <el-step title="商品详情" />
          <el-step title="其他设置" />
          <el-step title="规格设置" />
        </el-steps>
      </div>
      <el-form ref="formValidate" v-loading="fullscreenLoading" class="formValidate mt20" :rules="ruleValidate" :model="formValidate" label-width="120px" @submit.native.prevent>
        <el-row v-show="currentTab === 0" :gutter="24">
          <!-- 商品信息-->
          <el-col v-bind="grid2">
            <el-form-item label="商品名称：" prop="store_name">
              <el-input v-model="formValidate.store_name" placeholder="请输入商品名称" />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid2">
            <el-form-item label="商户商品分类：" prop="mer_cate_id">
              <el-cascader
                v-model="formValidate.mer_cate_id"
                class="selWidth"
                :options="merCateList"
                :props="propsMer"
                filterable
                clearable
              />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid2">
            <el-form-item label="平台商品分类：" prop="cate_id">
              <el-cascader
                v-model="formValidate.cate_id"
                class="selWidth"
                :options="categoryList"
                :props="props"
                filterable
                clearable
              />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid2">
            <el-form-item label="品牌选择：" prop="brand_id">
              <el-select v-model="formValidate.brand_id" filterable placeholder="请选择" class="selWidth">
                <el-option
                  v-for="item in BrandList"
                  :key="item.brand_id"
                  :label="item.brand_name"
                  :value="item.brand_id"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid2">
            <el-form-item label="商品关键字：">
              <el-input v-model="formValidate.keyword" placeholder="请输入商品关键字" />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid2">
            <el-form-item label="单位：" prop="unit_name">
              <el-input v-model="formValidate.unit_name" placeholder="请输入单位" />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid2">
            <el-form-item label="商品简介：" prop="store_info">
              <el-input v-model="formValidate.store_info" type="textarea" :rows="3" placeholder="请输入商品简介" />
            </el-form-item>
          </el-col>
          <el-col v-bind="grid2">
            <el-form-item label="商品封面图：" prop="image">
              <div class="upLoadPicBox" @click="modalPicTap('1')" title="750*750px">
                <div v-if="formValidate.image" class="pictrue"><img :src="formValidate.image"></div>
                <div v-else class="upLoad">
                  <i class="el-icon-camera cameraIconfont" />
                </div>
              </div>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="商品轮播图：" prop="slider_image">

              <div class="acea-row">
                <div
                  v-for="(item,index) in formValidate.slider_image"
                  :key="index"
                  class="pictrue"
                  draggable="false"
                  @dragstart="handleDragStart($event, item)"
                  @dragover.prevent="handleDragOver($event, item)"
                  @dragenter="handleDragEnter($event, item)"
                  @dragend="handleDragEnd($event, item)"
                >
                  <img :src="item">
                  <i class="el-icon-error btndel" @click="handleRemove(index)" />
                </div>
                <div v-if="formValidate.slider_image.length < 10" class="uploadCont" title="750*750px">
                  <div class="upLoadPicBox" @click="modalPicTap('2')">
                    <div class="upLoad">
                      <i class="el-icon-camera cameraIconfont" />
                    </div>
                  </div>

                </div>
              </div>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="运费模板：" prop="temp_id">
              <div class="acea-row">
                <el-select v-model="formValidate.temp_id" placeholder="请选择" class="selWidthd mr20">
                  <el-option
                    v-for="item in shippingList"
                    :key="item.shipping_template_id"
                    :label="item.name"
                    :value="item.shipping_template_id"
                  />
                </el-select>
                <el-button class="mr15" size="small" @click="addTem">添加运费模板</el-button>
              </div>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="优惠券：" class="proCoupon">
              <div class="acea-row">
                <el-tag
                  v-for="(tag, index) in formValidate.couponData"
                  :key="index"
                  class="mr10"
                  closable
                  :disable-transitions="false"
                  @close="handleCloseCoupon(tag)"
                >
                  {{ tag.title }}
                </el-tag>
                <el-button class="mr15" size="mini" @click="addCoupon">选择优惠券</el-button>
              </div>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- 商品详情-->
        <el-row v-show="currentTab === 1">
          <el-col :span="24">
            <el-form-item label="商品详情：">
<!--              <vue-ueditor-wrap v-model="formValidate.content" @beforeInit="addCustomDialog"  :config="myConfig"></vue-ueditor-wrap>-->
              <ueditorFrom v-model="formValidate.content" :content="formValidate.content"/>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- 其他设置-->
        <el-row v-show="currentTab === 2">
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="排序：">
                <el-input-number v-model="formValidate.sort" placeholder="请输入排序" />
              </el-form-item>
            </el-col>
          </el-col>
          <el-col :span="24">
            <el-form-item label="商品推荐：">
              <el-checkbox-group v-model="checkboxGroup" size="small" @change="onChangeGroup">
                <el-checkbox v-for="(item, index) in recommend" :key="index" :label="item.value">{{ item.name }}</el-checkbox>
              </el-checkbox-group>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="是否开启礼包：">
              <el-radio-group v-model="formValidate.is_gift_bag" :disabled="$route.params.id?true:false">
                <el-radio :label="0" class="radio">否</el-radio>
                <el-radio :label="1">是</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row v-show="currentTab === 3">
          <el-col :span="24">
            <el-form-item label="商品规格：" props="spec_type">
              <el-radio-group v-model="formValidate.spec_type" @change="onChangeSpec(formValidate.spec_type)">
                <el-radio :label="0" class="radio">单规格</el-radio>
                <el-radio :label="1">多规格</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item v-if="extensionStatus>0" label="佣金设置：" props="extension_type">
              <el-radio-group v-model="formValidate.extension_type" @change="onChangetype(formValidate.extension_type)">
                <el-radio :label="1" class="radio">单独设置</el-radio>
                <el-radio :label="0">默认设置</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
          <!-- 多规格添加-->
          <el-col v-if="formValidate.spec_type === 1" :span="24" class="noForm">
            <el-form-item label="选择规格：" prop="">
              <div class="acea-row">
                <el-select v-model="selectRule">
                  <el-option v-for="item in ruleList" :key="item.attr_template_id" :label="item.template_name" :value="item.attr_template_id" />
                </el-select>
                <el-button type="primary" class="mr20" @click="confirm">确认</el-button>
                <el-button class="mr15" @click="addRule">添加规格模板</el-button>
              </div>
            </el-form-item>
            <el-form-item v-if="formValidate.attr.length > 0">
              <div v-for="(item, index) in formValidate.attr" :key="index">
                <div class="acea-row row-middle"><span class="mr5">{{ item.value }}</span><i class="el-icon-circle-close" @click="handleRemoveAttr(index)" /></div>
                <div class="rulesBox">
                  <el-tag
                    v-for="(j, indexn) in item.detail"
                    :key="indexn"
                    closable
                    size="medium"
                    :disable-transitions="false"
                    class="mb5 mr10"
                    @close="handleClose(item.detail,indexn)"
                  >
                    {{ j }}
                  </el-tag>
                  <el-input
                    v-if="item.inputVisible"
                    ref="saveTagInput"
                    v-model="item.detail.attrsVal"
                    class="input-new-tag"
                    size="small"
                    @keyup.enter.native="createAttr(item.detail.attrsVal,index)"
                    @blur="createAttr(item.detail.attrsVal,index)"
                  />
                  <el-button v-else class="button-new-tag" size="small" @click="showInput(item)">+ 添加</el-button>
                </div>
              </div>
            </el-form-item>
            <el-col v-if="isBtn">
              <el-col :xl="6" :lg="9" :md="9" :sm="24" :xs="24">
                <el-form-item label="规格：">
                  <el-input v-model="formDynamic.attrsName" placeholder="请输入规格" />
                </el-form-item>
              </el-col>
              <el-col :xl="6" :lg="9" :md="9" :sm="24" :xs="24">
                <el-form-item label="规格值：">
                  <el-input v-model="formDynamic.attrsVal" placeholder="请输入规格值" />
                </el-form-item>
              </el-col>
              <el-col :xl="12" :lg="6" :md="6" :sm="24" :xs="24">
                <el-form-item class="noLeft">
                  <el-button type="primary" class="mr15" @click="createAttrName">确定</el-button>
                  <el-button @click="offAttrName">取消</el-button>
                </el-form-item>
              </el-col>
            </el-col>
            <el-form-item v-if="!isBtn">
              <el-button type="primary" icon="md-add" class="mr15" @click="addBtn">添加新规格</el-button>
            </el-form-item>
          </el-col>
          <!-- 批量设置-->
          <el-col v-if="formValidate.spec_type === 1 && formValidate.attr.length>0" :span="24" class="noForm">
            <el-form-item label="批量设置：" class="labeltop">
              <el-table :data="oneFormBatch" border class="tabNumWidth" size="mini">
                <el-table-column align="center" label="图片" min-width="80">
                  <template slot-scope="scope">
                    <div class="upLoadPicBox" @click="modalPicTap('1','pi')" title="750*750px">
                      <div v-if="scope.row.image" class="pictrue tabPic"><img :src="scope.row.image"></div>
                      <div v-else class="upLoad tabPic">
                        <i class="el-icon-camera cameraIconfont" />
                      </div>
                    </div>
                  </template>
                </el-table-column>
                <el-table-column v-for="(item,iii) in attrValue" :key="iii" :label="formThead[iii].title" align="center" min-width="120">
                  <template slot-scope="scope">
                    <el-input v-model="scope.row[iii]" :type="formThead[iii].title==='商品编号'?'text':'number'" :min="0" class="priceBox" />
                  </template>
                </el-table-column>
                <template v-if="formValidate.extension_type === 1">
                  <el-table-column align="center" label="一级返佣(元)" min-width="120">
                    <template slot-scope="scope">
                      <el-input v-model="scope.row.extension_one" type="number" :min="0" class="priceBox" />
                    </template>
                  </el-table-column>
                  <el-table-column align="center" label="二级返佣(元)" min-width="120">
                    <template slot-scope="scope">
                      <el-input v-model="scope.row.extension_two" type="number" :min="0" class="priceBox" />
                    </template>
                  </el-table-column>
                </template>
                <el-table-column align="center" label="操作" min-width="80">
                  <template>
                    <el-button type="text" class="submission" @click="batchAdd">批量添加</el-button>
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
          </el-col>
          <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
            <!-- 单规格表格-->
            <el-form-item v-if="formValidate.spec_type === 0">
              <el-table :data="OneattrValue" border class="tabNumWidth" size="mini">
                <el-table-column align="center" label="图片" min-width="80">
                  <template slot-scope="scope">
                    <div class="upLoadPicBox" @click="modalPicTap('1', 'dan', 'pi')">
                      <div v-if="formValidate.image" class="pictrue tabPic"><img :src="scope.row.image"></div>
                      <div v-else class="upLoad tabPic">
                        <i class="el-icon-camera cameraIconfont" />
                      </div>
                    </div>
                  </template>
                </el-table-column>
                <el-table-column v-for="(item,iii) in attrValue" :key="iii" :label="formThead[iii].title" align="center" min-width="120">
                  <template slot-scope="scope">
                    <el-input v-model="scope.row[iii]" :type="formThead[iii].title==='商品编号'?'text':'number'" :min="0" class="priceBox" />
                  </template>
                </el-table-column>
                <template v-if="formValidate.extension_type === 1">
                  <el-table-column align="center" label="一级返佣(元)" min-width="120">
                    <template slot-scope="scope">
                      <el-input v-model="scope.row.extension_one" type="number" :min="0" class="priceBox" />
                    </template>
                  </el-table-column>
                  <el-table-column align="center" label="二级返佣(元)" min-width="120">
                    <template slot-scope="scope">
                      <el-input v-model="scope.row.extension_two" type="number" :min="0" class="priceBox" />
                    </template>
                  </el-table-column>
                </template>
              </el-table>
            </el-form-item>
            <!-- 多规格表格-->
            <el-form-item v-if="formValidate.spec_type === 1 && formValidate.attr.length>0" class="labeltop" label="规格列表：">
              <el-table :data="ManyAttrValue" border class="tabNumWidth" size="mini">
                <template v-if="manyTabDate">
                  <el-table-column v-for="(item,iii) in manyTabDate" :key="iii" align="center" :label="manyTabTit[iii].title" min-width="80">
                    <template slot-scope="scope">
                      <span class="priceBox" v-text="scope.row[iii]" />
                    </template>
                  </el-table-column>
                </template>
                <el-table-column align="center" label="图片" min-width="80">
                  <template slot-scope="scope">
                    <div class="upLoadPicBox" @click="modalPicTap('1','duo',scope.$index)" title="750*750px">
                      <div v-if="scope.row.image" class="pictrue tabPic"><img :src="scope.row.image"></div>
                      <div v-else class="upLoad tabPic">
                        <i class="el-icon-camera cameraIconfont" />
                      </div>
                    </div>
                  </template>
                </el-table-column>
                <el-table-column v-for="(item,iii) in attrValue" :key="iii" :label="formThead[iii].title" align="center" min-width="120">
                  <template slot-scope="scope">
                    <el-input v-model="scope.row[iii]" :type="formThead[iii].title==='商品编号'?'text':'number'" class="priceBox" />
                  </template>
                </el-table-column>
                <template v-if="formValidate.extension_type === 1">
                  <el-table-column align="center" label="一级返佣(元)" min-width="120">
                    <template slot-scope="scope">
                      <el-input v-model="scope.row.extension_one" type="number" :min="0" class="priceBox" />
                    </template>
                  </el-table-column>
                  <el-table-column align="center" label="二级返佣(元)" min-width="120">
                    <template slot-scope="scope">
                      <el-input v-model="scope.row.extension_two" type="number" :min="0" class="priceBox" />
                    </template>
                  </el-table-column>
                </template>
                <el-table-column key="3" align="center" label="操作" min-width="80">
                  <template slot-scope="scope">
                    <el-button type="text" class="submission" @click="delAttrTable(scope.$index)">删除</el-button>
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item style="margin-top:30px;">
          <el-button v-show="currentTab>0" type="primary" class="submission" size="small" @click="handleSubmitUp">上一步</el-button>
          <el-button v-show="currentTab<3" type="primary" class="submission" size="small" @click="handleSubmitNest('formValidate')">下一步</el-button>
          <el-button v-show="currentTab===3 || $route.params.id" :loading="loading" type="primary" class="submission" size="small" @click="handleSubmit('formValidate')">提交</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import ueditorFrom from '@/components/ueditorFrom'
import VueUeditorWrap from 'vue-ueditor-wrap';
import { shippingListApi, templateLsitApi, productCreateApi, productDetailApi, categorySelectApi, categoryListApi, categoryBrandListApi, productUpdateApi, productConfigApi } from '@/api/product'
import { roterPre } from '@/settings'
const defaultObj = {
  image: '',
  slider_image: [],
  store_name: '',
  store_info: '',
  keyword: '',
  brand_id: '', // 品牌id
  cate_id: '', // 平台分类id
  mer_cate_id: [], // 商户分类id
  unit_name: '',
  sort: 0,
  is_good: 0,
  temp_id: '',
  attrValue: [{
    image: '',
    price: null,
    cost: null,
    ot_price: null,
    stock: null,
    bar_code: '',
    weight: null,
    volume: null
  }],
  attr: [],
  extension_type: 0,
  content: '',
  spec_type: 0,
  give_coupon_ids: [],
  is_gift_bag: 0,
  couponData: []
}
const objTitle = {
  price: {
    title: '售价'
  },
  cost: {
    title: '成本价'
  },
  ot_price: {
    title: '原价'
  },
  stock: {
    title: '库存'
  },
  bar_code: {
    title: '商品编号'
  },
  weight: {
    title: '重量（KG）'
  },
  volume: {
    title: '体积(m³)'
  }
}
const proOptions = [{ name: '店铺推荐', value: 'is_good' }]
export default {
  name: 'ProductProductAdd',
  components: { ueditorFrom, VueUeditorWrap },
  data() {
    return {
      myConfig: {
        autoHeightEnabled: false, // 编辑器不自动被内容撑高
        initialFrameHeight: 500, // 初始容器高度
        initialFrameWidth: '100%', // 初始容器宽度
        UEDITOR_HOME_URL: '/UEditor/',
        serverUrl: ''
      },
      optionsCate: {
        value: 'store_category_id',
        label: 'cate_name',
        children: 'children',
        emitPath: false
      },
      roterPre: roterPre,
      selectRule: '',
      checkboxGroup: [],
      recommend: proOptions,
      tabs: [],
      fullscreenLoading: false,
      props: { emitPath: false },
      propsMer: { emitPath: false, multiple: true },
      active: 0,
      OneattrValue: [Object.assign({}, defaultObj.attrValue[0])], // 单规格
      ManyAttrValue: [Object.assign({}, defaultObj.attrValue[0])], // 多规格
      ruleList: [],
      merCateList: [], // 商户分类筛选
      categoryList: [], // 平台分类筛选
      shippingList: [], // 运费模板
      BrandList: [], // 品牌
      formThead: Object.assign({}, objTitle),
      formValidate: Object.assign({}, defaultObj),
      picValidate: true,
      formDynamics: {
        template_name: '',
        template_value: []
      },
      manyTabTit: {},
      manyTabDate: {},
      grid2: {
        xl: 10,
        lg: 12,
        md: 12,
        sm: 24,
        xs: 24
      },
      // 规格数据
      formDynamic: {
        attrsName: '',
        attrsVal: ''
      },
      isBtn: false,
      manyFormValidate: [],
      images: [],
      currentTab: 0,
      isChoice: '',
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24
      },
      loading: false,
      ruleValidate: {
        give_coupon_ids: [
          { required: true, message: '请选择优惠券', trigger: 'change', type: 'array' }
        ],
        store_name: [
          { required: true, message: '请输入商品名称', trigger: 'blur' }
        ],
        mer_cate_id: [
          { required: true, message: '请选择商户分类', trigger: 'change', type: 'array', min: '1' }
        ],
        cate_id: [
          { required: true, message: '请选择平台分类', trigger: 'change' }
        ],
        keyword: [
          { required: true, message: '请输入商品关键字', trigger: 'blur' }
        ],
        unit_name: [
          { required: true, message: '请输入单位', trigger: 'blur' }
        ],
        store_info: [
          { required: true, message: '请输入商品简介', trigger: 'blur' }
        ],
        temp_id: [
          { required: true, message: '请选择运费模板', trigger: 'change' }
        ],
        brand_id: [
          { required: true, message: '请选择品牌', trigger: 'change' }
        ],
        image: [
          { required: true, message: '请上传商品图', trigger: 'change' }
        ],
        slider_image: [
          { required: true, message: '请上传商品轮播图', type: 'array', trigger: 'change' }
        ],
        spec_type: [
          { required: true, message: '请选择商品规格', trigger: 'change' }
        ]
      },
      attrInfo: {},
      keyNum: 0,
      extensionStatus: 0
    }
  },
  computed: {
    attrValue() {
      const obj = Object.assign({}, defaultObj.attrValue[0])
      delete obj.image
      return obj
    },
    oneFormBatch() {
      const obj = [Object.assign({}, defaultObj.attrValue[0])]
      delete obj[0].bar_code
      return obj
    }
  },
  watch: {
    'formValidate.attr': {
      handler: function(val) {
        if (this.formValidate.spec_type === 1) this.watCh(val)
      },
      immediate: false,
      deep: true
    }
  },
  created() {
    this.tempRoute = Object.assign({}, this.$route)
    if (this.$route.params.id && this.formValidate.spec_type === 1) {
      this.$watch('formValidate.attr', this.watCh)
    }
  },
  mounted() {
    this.formValidate.slider_image = []
    if (this.$route.params.id) {
      this.setTagsViewTitle()
      this.getInfo()
    }
    this.formValidate.attr.map(item => {
      this.$set(item, 'inputVisible', false)
    })
    this.getCategorySelect()
    this.getCategoryList()
    this.getBrandListApi()
    this.getShippingList()
    this.productCon()
  },
  methods: {
    handleCloseCoupon(tag) {
      this.formValidate.couponData.splice(this.formValidate.couponData.indexOf(tag), 1)
      this.formValidate.give_coupon_ids = []
      this.formValidate.couponData.map((item) => {
        this.formValidate.give_coupon_ids.push(item.coupon_id)
      })
    },
    productCon() {
      productConfigApi().then(res => {
        this.extensionStatus = res.data.extension_status
      }).catch(res => {
        this.$message.error(res.message)
      })
    },
    addCoupon() {
      const _this = this
      this.$modalCoupon(this.formValidate.couponData, 'wu', _this.formValidate.give_coupon_ids, this.keyNum += 1, function(row) {
        _this.formValidate.give_coupon_ids = []
        _this.formValidate.couponData = row
        row.map((item) => {
          _this.formValidate.give_coupon_ids.push(item.coupon_id)
          // _this.couponData.push(item.title)
        })
      })
    },
    setTagsViewTitle() {
      const title = '编辑商品'
      const route = Object.assign({}, this.tempRoute, { title: `${title}-${this.$route.params.id}` })
      this.$store.dispatch('tagsView/updateVisitedView', route)
    },
    onChangeGroup() {
      this.checkboxGroup.includes('is_good') ? this.formValidate.is_good = 1 : this.formValidate.is_good = 0
    },
    watCh(val) {
      const tmp = {}
      const tmpTab = {}
      this.formValidate.attr.forEach((o, i) => {
        tmp['value' + i] = { title: o.value }
        tmpTab['value' + i] = ''
      })
      console.log(this.ManyAttrValue);
      this.ManyAttrValue = this.attrFormat(val)
      this.ManyAttrValue.forEach((val, index) => {
        const key = Object.values(val.detail).sort().join('/')
        if (this.attrInfo[key]) this.ManyAttrValue[index] = this.attrInfo[key]

      })
      this.attrInfo = {}
      this.ManyAttrValue.forEach((val) => {
        // this.attrInfo[Object.values(val.detail).sort().join('/')] = val
        if(val.detail !== 'undefined' && val.detail !== null){
          this.attrInfo[Object.values(val.detail).sort().join('/')] = val
        }
      })
      this.manyTabTit = tmp
      this.manyTabDate = tmpTab
      this.formThead = Object.assign({}, this.formThead, tmp)
      console.log(this.ManyAttrValue);
    },
    attrFormat(arr) {
      let data = []
      const res = []
      return format(arr)
      function format(arr) {
        if (arr.length > 1) {
          arr.forEach((v, i) => {
            if (i === 0) data = arr[i]['detail']
            const tmp = []
            data.forEach(function(vv) {
              arr[i + 1] && arr[i + 1]['detail'] && arr[i + 1]['detail'].forEach(g => {
                const rep2 = (i !== 0 ? '' : arr[i]['value'] + '_$_') + vv + '-$-' + arr[i + 1]['value'] + '_$_' + g
                tmp.push(rep2)
                if (i === (arr.length - 2)) {
                  const rep4 = {
                    image: '',
                    price: 0,
                    cost: 0,
                    ot_price: 0,
                    stock: 0,
                    bar_code: '',
                    weight: 0,
                    volume: 0,
                    brokerage: 0,
                    brokerage_two: 0
                  }
                  rep2.split('-$-').forEach((h, k) => {
                    const rep3 = h.split('_$_')
                    if (!rep4['detail']) rep4['detail'] = {}
                    rep4['detail'][rep3[0]] = rep3.length > 1 ? rep3[1] : ''
                  })
                  // if(rep4.detail !== 'undefined' && rep4.detail !== null){
                    Object.values(rep4.detail).forEach((v, i) => {
                      rep4['value' + i] = v
                    })
                  // }

                  res.push(rep4)
                }
              })
            })
            data = tmp.length ? tmp : []
          })
        } else {
          const dataArr = []
          arr.forEach((v, k) => {
            v['detail'].forEach((vv, kk) => {
              dataArr[kk] = v['value'] + '_' + vv
              res[kk] = {
                image: '',
                price: 0,
                cost: 0,
                ot_price: 0,
                stock: 0,
                bar_code: '',
                weight: 0,
                volume: 0,
                brokerage: 0,
                brokerage_two: 0,
                detail: { [v['value']]: vv }
              }
              // if(res[kk].detail !== 'undefined' && res[kk].detail !== null){
                Object.values(res[kk].detail).forEach((v, i) => {
                  res[kk]['value' + i] = v
                })
              // }
            })
          })
          data.push(dataArr.join('$&'))
        }
        return res
      }
    },
    // watCh(val) {
    //   const tmp = {}
    //   const tmpTab = {}
    //   this.formValidate.attr.forEach((o, i) => {
    //     tmp['value' + i] = { title: o.value }
    //     tmpTab['value' + i] = ''
    //   })
    //   this.ManyAttrValue = this.attrFormat(val)
    //   if(this.formValidate.attrValue.length > 1) this.ManyAttrValue = Object.assign(this.ManyAttrValue,this.formValidate.attrValue)
    //   this.ManyAttrValue.forEach((val, index) => {
    //     if(val.detail){
    //       const key = Object.values(val.detail).sort().join('/')
    //       if (this.attrInfo[key]) this.ManyAttrValue[index] = this.attrInfo[key]
    //     }
    //
    //   })
    //   console.log(this.ManyAttrValue)
    //   this.manyTabTit = tmp
    //   this.manyTabDate = tmpTab
    //   this.formThead = Object.assign({}, this.formThead, tmp)
    // },
    // attrFormat(arr) {
    //   let data = []
    //   const res = []
    //   return format(arr)
    //   function format(arr) {
    //     if (arr.length > 1) {
    //       arr.forEach((v, i) => {
    //         if (i === 0) data = arr[i]['detail']
    //         const tmp = []
    //         data.forEach(function(vv) {
    //           arr[i + 1] && arr[i + 1]['detail'] && arr[i + 1]['detail'].forEach(g => {
    //             const rep2 = (i !== 0 ? '' : arr[i]['value'] + '_') + vv + '-' + arr[i + 1]['value'] + '_' + g
    //             tmp.push(rep2)
    //             if (i === (arr.length - 2)) {
    //               const rep4 = {
    //                 image: '',
    //                 price: 0,
    //                 cost: 0,
    //                 ot_price: 0,
    //                 stock: 0,
    //                 bar_code: '',
    //                 weight: 0,
    //                 volume: 0,
    //                 brokerage: 0,
    //                 brokerage_two: 0
    //               }
    //               rep2.split('-').forEach((h, k) => {
    //                 const rep3 = h.split('_')
    //                 if (!rep4['detail']) rep4['detail'] = {}
    //                 rep4['detail'][rep3[0]] = rep3.length > 1 ? rep3[1] : ''
    //               })
    //               Object.values(rep4.detail).forEach((v, i) => {
    //                 rep4['value' + i] = v
    //               })
    //               res.push(rep4)
    //             }
    //           })
    //         })
    //         data = tmp.length ? tmp : []
    //       })
    //     } else {
    //       const dataArr = []
    //       arr.forEach((v, k) => {
    //         v['detail'].forEach((vv, kk) => {
    //           dataArr[kk] = v['value'] + '_' + vv
    //           res[kk] = {
    //             image: '',
    //             price: 0,
    //             cost: 0,
    //             ot_price: 0,
    //             stock: 0,
    //             bar_code: '',
    //             weight: 0,
    //             volume: 0,
    //             brokerage: 0,
    //             brokerage_two: 0,
    //             detail: { [v['value']]: vv }
    //           }
    //           Object.values(res[kk].detail).forEach((v, i) => {
    //             res[kk]['value' + i] = v
    //           })
    //         })
    //       })
    //       data.push(dataArr.join('$&'))
    //     }
    //     return res
    //   }
    // },
    // 运费模板
    addTem() {
      const _this = this
      this.$modalTemplates(0, function() {
        _this.getShippingList()
      })
    },
    // 添加规则；
    addRule() {
      const _this = this
      this.$modalAttr(this.formDynamics, function() {
        _this.productGetRule()
      })
    },
    // 选择规格
    onChangeSpec(num) {
      if (num === 1) this.productGetRule()
    },
    // 选择属性确认
    confirm() {
      if (!this.selectRule) {
        return this.$message.warning('请选择属性')
      }
      this.ruleList.forEach(item => {
        if (item.attr_template_id === this.selectRule) {
          this.formValidate.attr = item.template_value
        }
      })
    },
    // 商户分类；
    getCategorySelect() {
      categorySelectApi().then(res => {
        this.merCateList = res.data
      }).catch(res => {
        this.$message.error(res.message)
      })
    },
    // 平台分类；
    getCategoryList() {
      categoryListApi().then(res => {
        this.categoryList = res.data
      }).catch(res => {
        this.$message.error(res.message)
      })
    },
    // 品牌筛选；
    getBrandListApi() {
      categoryBrandListApi().then(res => {
        this.BrandList = res.data
      }).catch(res => {
        this.$message.error(res.message)
      })
    },
    // 获取商品属性模板；
    productGetRule() {
      templateLsitApi().then(res => {
        this.ruleList = res.data
      })
    },
    // 运费模板；
    getShippingList() {
      shippingListApi().then(res => {
        this.shippingList = res.data
      })
    },
    showInput(item) {
      this.$set(item, 'inputVisible', true)
    },
    onChangetype(item) {
      if (item === 1) {
        this.OneattrValue.map(item => {
          this.$set(item, 'extension_one', null)
          this.$set(item, 'extension_two', null)
        })
        this.ManyAttrValue.map(item => {
          this.$set(item, 'extension_one', null)
          this.$set(item, 'extension_two', null)
        })
      } else {
        this.OneattrValue.map(item => {
          delete item.extension_one
          delete item.extension_two
          this.$set(item, 'extension_one', null)
          this.$set(item, 'extension_two', null)
        })
        this.ManyAttrValue.map(item => {
          delete item.extension_one
          delete item.extension_two
        })
      }
    },
    // 删除表格中的属性
    delAttrTable(index) {
      this.ManyAttrValue.splice(index, 1)
    },
    // 批量添加
    batchAdd() {
      for (const val of this.ManyAttrValue) {
        this.$set(val, 'image', this.oneFormBatch[0].image)
        this.$set(val, 'price', this.oneFormBatch[0].price)
        this.$set(val, 'cost', this.oneFormBatch[0].cost)
        this.$set(val, 'ot_price', this.oneFormBatch[0].ot_price)
        this.$set(val, 'stock', this.oneFormBatch[0].stock)
        this.$set(val, 'bar_code', this.oneFormBatch[0].bar_code)
        this.$set(val, 'weight', this.oneFormBatch[0].weight)
        this.$set(val, 'volume', this.oneFormBatch[0].volume)
        this.$set(val, 'extension_one', this.oneFormBatch[0].extension_one)
        this.$set(val, 'extension_two', this.oneFormBatch[0].extension_two)
      }
    },
    // 添加按钮
    addBtn() {
      this.clearAttr()
      this.isBtn = true
    },
    // 取消
    offAttrName() {
      this.isBtn = false
    },
    clearAttr() {
      this.formDynamic.attrsName = ''
      this.formDynamic.attrsVal = ''
    },
    // 删除规格
    handleRemoveAttr(index) {
      this.formValidate.attr.splice(index, 1)
      this.manyFormValidate.splice(index, 1)
    },
    // 删除属性
    handleClose(item, index) {
      item.splice(index, 1)
    },
    // 添加规则名称
    createAttrName() {
      if (this.formDynamic.attrsName && this.formDynamic.attrsVal) {
        const data = {
          value: this.formDynamic.attrsName,
          detail: [
            this.formDynamic.attrsVal
          ]
        }
        this.formValidate.attr.push(data)
        var hash = {}
        this.formValidate.attr = this.formValidate.attr.reduce(function(item, next) {
          /* eslint-disable */
            hash[next.value] ? '' : hash[next.value] = true && item.push(next)
            return item
          }, [])
          this.clearAttr()
          this.isBtn = false
        } else {
          this.$message.warning('请添加完整的规格！');
        }
      },
      // 添加属性
      createAttr (num, idx) {
        if (num) {
          this.formValidate.attr[idx].detail.push(num);
          var hash = {};
          this.formValidate.attr[idx].detail = this.formValidate.attr[idx].detail.reduce(function (item, next) {
            /* eslint-disable */
            hash[next] ? '' : hash[next] = true && item.push(next);
            return item
          }, [])
          this.formValidate.attr[idx].inputVisible = false
        } else {
          this.$message.warning('请添加属性');
        }
      },
      // 详情
      getInfo () {
        this.fullscreenLoading = true
        productDetailApi(this.$route.params.id).then(async res => {
          let info = res.data;
          this.formValidate = {
            image: info.image,
            attrValue: info.attrValue,
            slider_image: info.slider_image,
            store_name: info.store_name,
            store_info: info.store_info,
            keyword: info.keyword,
            brand_id: info.brand_id, // 品牌id
            cate_id: info.cate_id, // 平台分类id
            mer_cate_id: info.mer_cate_id, // 商户分类id
            unit_name: info.unit_name,
            sort: info.sort,
            is_good: info.is_good,
            temp_id: info.temp_id,
            attr: info.attr,
            extension_type: info.extension_type,
            content: info.content,
            spec_type: info.spec_type,
            give_coupon_ids: info.give_coupon_ids,
            is_gift_bag: info.is_gift_bag,
            couponData: info.coupon
          }
          if(this.formValidate.spec_type === 0){
            this.OneattrValue = info.attrValue
          }else{
            this.ManyAttrValue = info.attrValue
            this.ManyAttrValue.forEach((val) => {
              if(val.detail !== 'undefined' && val.detail !== null){
                this.attrInfo[Object.values(val.detail).sort().join('/')] = val
              }

            })
          }
          if(this.formValidate.is_good===1)this.checkboxGroup.push('is_good')
          this.fullscreenLoading = false
        }).catch(res => {
          this.fullscreenLoading = false
          this.$message.error(res.message);
        })
      },
      handleRemove (i) {
        this.formValidate.slider_image.splice(i, 1);
      },
      // 点击商品图
      modalPicTap (tit, num, i) {
        const _this = this
        const attr = []
        this.$modalUpload(function(img) {
            if(tit==='1'&& !num){
              _this.formValidate.image = img[0]
              _this.OneattrValue[0].image = img[0]
            }
            if(tit==='2'&& !num){
              img.map((item) => {
                attr.push(item.attachment_src)
                _this.formValidate.slider_image.push(item)
                if(_this.formValidate.slider_image.length > 10) _this.formValidate.slider_image.length = 10
              });
            }
            if(tit==='1'&& num === 'dan' ){
              _this.OneattrValue[0].image = img[0]
            }
            if(tit==='1'&& num === 'duo' ){
              _this.ManyAttrValue[i].image = img[0]
            }
            if(tit==='1'&& num === 'pi' ){
              _this.oneFormBatch[0].image = img[0]
            }
            },tit)
      },
      handleSubmitUp(){
        if (this.currentTab-- <0) this.currentTab = 0;
      },
      handleSubmitNest(name){
        this.$refs[name].validate((valid) => {
          if (valid) {
            if (this.currentTab++ > 2) this.currentTab = 0;
          } else {
            if(!this.formValidate.store_name || !this.formValidate.cate_id
              || !this.formValidate.unit_name || !this.formValidate.store_info
              || !this.formValidate.image || !this.formValidate.slider_image){
              this.$message.warning("请填写完整商品信息！");
            }
          }
        })
      },
      // 提交
      handleSubmit (name) {
        this.onChangeGroup()
        if(this.formValidate.spec_type === 1){
          this.formValidate.attrValue=this.ManyAttrValue
        }else{
          this.formValidate.attrValue=this.OneattrValue
          this.formValidate.attr = []
        }
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.fullscreenLoading = true
            this.loading = true
            this.$route.params.id?productUpdateApi(this.$route.params.id,this.formValidate).then(async res => {
              this.fullscreenLoading = false
              this.$message.success(res.message);
              this.$router.push({ path: this.roterPre +'/product/list' });
              this.$refs[name].resetFields();
              this.formValidate.slider_image = []
              this.loading = false
            }).catch(res => {
              this.fullscreenLoading = false
              this.loading = false
              this.$message.error(res.message);
            }):productCreateApi(this.formValidate).then(async res => {
              this.fullscreenLoading = false
              this.$message.success(res.message);
              this.$router.push({ path: this.roterPre + '/product/list' });
              this.$refs[name].resetFields();
              this.formValidate.slider_image = []
              this.loading = false
            }).catch(res => {
              this.fullscreenLoading = false
              this.loading = false
              this.$message.error(res.message);
            })
          } else {
            if(!this.formValidate.store_name || !this.formValidate.cate_id
              || !this.formValidate.unit_name || !this.formValidate.store_info || !this.formValidate.image || !this.formValidate.slider_image){
              this.$message.warning("请填写完整商品信息！");
            }
          }
        })
      },
      // 表单验证
      validate (prop, status, error) {
        if (status === false) {
          this.$message.warning(error);
        }
      },
      // 规格图片验证
      specPicValidate(ManyAttrValue){
        for (let i = 0; i < ManyAttrValue.length; i++) {
          if(ManyAttrValue[i].image === "" || !ManyAttrValue[i].image) {
            this.$message.warning("请上传商品规格图！");
            this.picValidate = false;
            break;
          }
        }
      },
      // 移动
      handleDragStart (e, item) {
        this.dragging = item;
      },
      handleDragEnd (e, item) {
        this.dragging = null
      },
      handleDragOver (e) {
        e.dataTransfer.dropEffect = 'move'
      },
      handleDragEnter (e, item) {
        e.dataTransfer.effectAllowed = 'move'
        if (item === this.dragging) {
          return
        }
        const newItems = [...this.formValidate.slider_image]
        const src = newItems.indexOf(this.dragging)
        const dst = newItems.indexOf(item)
        newItems.splice(dst, 0, ...newItems.splice(src, 1))
        this.formValidate.slider_image = newItems;
      },
    // 添加自定义弹窗
    addCustomDialog (editorId) {
      // window.UE.registerUI('video-dialog', function (editor, uiName) {
      //   let dialog = new window.UE.ui.Dialog({
      //     iframeUrl: '/admin/widget.video/index.html?fodder=video',
      //     editor: editor,
      //     name: uiName,
      //     title: '上传视频',
      //     cssRules: 'width:1000px;height:500px;padding:20px;'
      //   });
      //   this.dialog = dialog;
      //   let btn = new window.UE.ui.Button({
      //     name: 'video-button',
      //     title: '上传视频',
      //     cssRules: `background-image: url(../../../assets/images/icons.png);background-position: -320px -20px;`,
      //     onclick: function () {
      //       // 渲染dialog
      //       dialog.render();
      //       dialog.open();
      //     }
      //   });
      //   return btn;
      // }, 38);
    }
    }
  }
</script>
<style scoped lang="scss">
  /deep/ .upLoadPicBox{
    .upLoad{
      -webkit-box-orient: vertical;
      -moz-box-orient: vertical;
      -o-box-orient: vertical;
      -webkit-flex-direction: column;
      -ms-flex-direction: column;
      flex-direction: column;
      line-height: 20px;
    }
    span{
      font-size: 10px;
    }
  }
  .proCoupon{
    /deep/.el-form-item__content{
      margin-top: 5px;
    }
  }
  .tabPic{
    width: 40px !important;
    height: 40px !important;
    img{
      width: 100%;
      height: 100%;
    }
  }
  .noLeft{
    /deep/.el-form-item__content{
      margin-left: 0 !important;
    }
  }
  .tabNumWidth{
    /deep/.el-input-number--medium{
      width: 121px !important;
    }
    /deep/.el-input-number__increase{
       width: 20px !important;
       font-size: 12px !important;
     }
    /deep/.el-input-number__decrease{
      width: 20px !important;
      font-size: 12px !important;
    }
    /deep/.el-input-number--medium .el-input__inner {
      padding-left: 25px !important;
      padding-right: 25px !important;
    }
    /deep/ thead{
      line-height: normal !important;
    }
    /deep/ .el-table .cell{
      line-height: normal !important;
    }
  }
  .selWidth{
    width: 100%;
  }
  .selWidthd{
    width: 300px;
  }
  .button-new-tag {
    height: 28px;
    line-height: 26px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    margin-left: 10px;
    vertical-align: bottom;
  }
  .pictrue{
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0,0,0,0.1);
    margin-right: 10px;
    position: relative;
    cursor: pointer;
    img{
      width: 100%;
      height: 100%;
    }
  }
  .btndel{
    position: absolute;
    z-index: 1;
    width :20px !important;
    height: 20px !important;
    left: 46px;
    top: -4px;
  }
  .labeltop{
    /deep/.el-form-item__label{
      float: none !important;
      display: inline-block !important;
      margin-left: 120px !important;
      width: auto !important;
    }
  }
</style>
