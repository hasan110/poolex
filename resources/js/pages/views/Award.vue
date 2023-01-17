<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            جوایز
          </h3>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>عنوان جایزه</th>
              <th>مقدار</th>
              <th>تعداد</th>
              <th>شماره تماس</th>
              <th>تصویر</th>
              <th>عملیات</th>
            </tr>
          </thead>
            
          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{item.title}}</td>
              <td>{{item.amount}}</td>
              <td>{{item.count}}</td>
              <td>
                <template v-if="item.need_call_number">نیاز هست</template>
                <template v-else>نیاز نیست</template>
              </td>
              <td>
                <a v-if="item.icon" target="_blank" :href="ImageUrl+item.icon"><img :src="ImageUrl+item.icon" alt="" class="profile-im" /></a>
                <template v-else>-</template>
              </td>
              <td>
                <button @click="getData(item.id)" class="btn btn-sm btn-primary">ویرایش</button>
                <button @click="deleteItem(item.id)" class="btn btn-sm btn-danger">حذف</button>
              </td>
            </tr>
          </tbody>
        </table>
    </div>

    <modal @close="createModal = false" :open="createModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="createModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          افزودن جایزه
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* عنوان جایزه  :</div>
            <div class="col-8">
              <input type="text" v-model="item.title" class="form-control">
                <div v-if="errors.title" class="invalid-feedback">
                  {{errors.title[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* مقدار  :</div>
            <div class="col-8">
              <input type="text" v-model="item.amount" class="form-control">
                <div v-if="errors.amount" class="invalid-feedback">
                  {{errors.amount[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* تعداد  :</div>
            <div class="col-8">
              <input type="text" v-model="item.count" class="form-control">
                <div v-if="errors.count" class="invalid-feedback">
                  {{errors.count[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* شماره تماس نیاز هست؟ :</div>
            <div class="col-8">
              <select v-model="item.need_call_number" class="form-control">
                <option value="1">بله</option>
                <option value="0">خیر</option>
              </select>
              <div v-if="errors.need_call_number" class="invalid-feedback">
                {{errors.need_call_number[0]}}
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* نوع  :</div>
            <div class="col-8">
              <select v-model="item.type" class="form-control">
                <option value="1">سکه</option>
                <option value="2">اعتبار حساب</option>
                <option value="3">شارژ</option>
                <option value="4">بسته اینترنت</option>
                <option value="5">جایزه</option>
              </select>
              <div v-if="errors.type" class="invalid-feedback">
                {{errors.type[0]}}
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">تصویر  :</div>
            <div class="col-8">
              <input ref="icon" placeholder="انتخاب تصویر" @change="selectIcon()" class="form-control form-control-sm" id="formFileSm" type="file">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="Create">افزودن</button>
        </div>
      </div>
    </modal>
    
    <modal @close="editModal = false" :open="editModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="editModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          ویرایش جایزه
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* عنوان جایزه  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_item.title" class="form-control">
                <div v-if="errors.title" class="invalid-feedback">
                  {{errors.title[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">*  مقدار :</div>
            <div class="col-8">
              <input type="text" v-model="edit_item.amount" class="form-control">
                <div v-if="errors.amount" class="invalid-feedback">
                  {{errors.amount[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* تعداد  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_item.count" class="form-control">
                <div v-if="errors.count" class="invalid-feedback">
                  {{errors.count[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* شماره تماس نیاز هست؟ :</div>
            <div class="col-8">
              <select v-model="edit_item.need_call_number" class="form-control">
                <option value="1">بله</option>
                <option value="0">خیر</option>
              </select>
              <div v-if="errors.need_call_number" class="invalid-feedback">
                {{errors.need_call_number[0]}}
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* نوع  :</div>
            <div class="col-8">
              <select v-model="edit_item.type" class="form-control">
                <option value="1">سکه</option>
                <option value="2">اعتبار حساب</option>
                <option value="3">شارژ</option>
                <option value="4">بسته اینترنت</option>
                <option value="5">جایزه</option>
              </select>
              <div v-if="errors.type" class="invalid-feedback">
                {{errors.type[0]}}
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">تصویر  :</div>
            <div class="col-8">
              <input ref="edit_icon" placeholder="انتخاب تصویر" @change="editIcon()" class="form-control form-control-sm" id="formFileSm" type="file">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="editItem">ویرایش</button>
        </div>
      </div>
    </modal>

    <button @click="createModal = !createModal" class="relative-btn">
      <i class="fa fa-plus"></i>
    </button>
    
  </div>
</template>
<script>
import Modal from '../../components/Modal.vue'
export default {
  name:'Award',
  components:{
    Modal
  },
  data(){
    return{
      list:{},
      item:{},
      edit_item:{},
      createModal:false,
      editModal:false,
      errors:{},
    }
  },
  methods:{
    selectIcon(){
      this.item.icon = this.$refs.icon.files[0];
    },
    editIcon(){
      this.edit_item.new_icon = this.$refs.edit_icon.files[0];
    },
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`awards/list`)
      .then(res => {
        this.list = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    Create(){
      this.SPIN_LOADING(1)
      const form = new FormData();
      const d = this.item
      d.title ? form.append('title', d.title) : ''
      d.amount ? form.append('amount', d.amount) : ''
      d.count ? form.append('count', d.count) : ''
      d.need_call_number ? form.append('need_call_number', d.need_call_number) : ''
      d.type ? form.append('type', d.type) : ''
      d.icon ? form.append('icon', d.icon) : ''
      
      this.$axios.post(`awards/add` , form)
      .then(res => {
        this.getList()
        this.createModal = false
        this.item = {}
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    getData(id){
      this.SPIN_LOADING(1)
      this.$axios.get(`awards/award/`+id)
      .then(res => {
        this.edit_item = res.data.data
        this.editModal = true
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    editItem(){
      this.SPIN_LOADING(1)
      const form = new FormData();
      const d = this.edit_item
      d.id ? form.append('id', d.id) : ''
      d.title ? form.append('title', d.title) : ''
      d.amount ? form.append('amount', d.amount) : ''
      d.count ? form.append('count', d.count) : ''
      d.need_call_number ? form.append('need_call_number', d.need_call_number) : ''
      d.type ? form.append('type', d.type) : ''
      d.new_icon ? form.append('new_icon', d.new_icon) : ''
      this.$axios.post(`awards/edit` , form)
      .then(res => {
        this.getList()
        this.editModal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    deleteItem(id){
      this.SPIN_LOADING(1)
      this.$axios.post(`awards/delete` , {id : id})
      .then(res => {
        this.getList()
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
  },
  mounted(){
    this.getList()
  }
}
</script>
<style>
</style>
