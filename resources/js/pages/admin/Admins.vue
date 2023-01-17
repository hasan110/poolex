<template>
  <div>
    <div class="content-title-green">
      <div class="col-container-3" >
        <div class="right-col" style="width:55%;" >
          <div class="row">
            <img :src="this.StaticUrl+'assets/img/checked_user_male_512px.png'" alt="" class="user-state-img">
            <p class="user-state-1">مدیریت ادمین ها</p>
          </div>
        </div>
        <div class="left-col">
          <div class="left-options">
            <div class="div-menu-item">
              <img :src="this.StaticUrl+'assets/img/squared_menu.png'" alt="">
              <p>فیلتر</p>
            </div>
            <input type="text" placeholder="جستجو">
            <div class="img-box-container">
              <img :src="this.StaticUrl+'assets/img/search_1.png'" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr style="border-radius: 10px;" class="tbl-row">
              <th>نام</th>
              <th>پست الکترونیکی</th>
              <th>نام کاربری</th>
              <th>رمز عبور</th>
              <th>عملیات</th>
            </tr>
          </thead>
            
          <tbody>
            <tr v-for="(item , key) in items" :key="key" class="tbl-row height">
              <th><p>{{item.name}}</p></th>
              <th><p>{{item.email}}</p></th>
              <th><p>{{item.user_name}}</p></th>
              <th><p>______</p></th>
              <th><p>
                <img @click="getAdmin(item.id)" :src="StaticUrl+'assets/img/icons/edit.svg'" alt="" class="profile-ic">
                <img @click="DeleteModal(item.id)" :src="StaticUrl+'assets/img/icons/trash.svg'" alt="" class="profile-ic">
              </p></th>
            </tr>
          </tbody>
        </table>
      <div v-if="current_page !== last_page" @click="loadMore" class="btm-box"><p>مشاهده بیشتر</p></div>
    </div>

    <!-- modals for operations -->
    <modal @close="createModal = false" :open="createModal">
        <div class="card m-b-0">
          <div class="card-header bg-primary">
            <span @click="createModal = false" class="close-modal"><i class="fa fa-times"></i></span>
            افزودن ادمین
          </div>
          <div class="card-body">
            <div class="row mb-2">
              <div class="col-4 text-left">نام :</div>
              <div class="col-8">
                <input type="text" v-model="admin.name" class="form-control">
                <div v-if="errors.name" class="invalid-feedback">
                  {{errors.name[0]}}
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-4 text-left">پست الکترونیکی :</div>
              <div class="col-8">
                <input type="text" v-model="admin.email" class="form-control">
                <div v-if="errors.email" class="invalid-feedback">
                  {{errors.email[0]}}
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-4 text-left">نام کاربری :</div>
              <div class="col-8">
                <input type="text" v-model="admin.user_name" class="form-control">
                <div v-if="errors.user_name" class="invalid-feedback">
                  {{errors.user_name[0]}}
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-4 text-left">رمز عبور :</div>
              <div class="col-8">
                <input type="text" v-model="admin.password" class="form-control">
                <div v-if="errors.password" class="invalid-feedback">
                  {{errors.password[0]}}
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button @click="addAdmin" class="btn btn-primary">افزودن</button>
          </div>
        </div>
    </modal>

    <modal @close="editModal = false" :open="editModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="editModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          ویرایش ادمین
        </div>
        <div class="card-body">
          <div class="row mb-2">
              <div class="col-4 text-left">نام  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_admin.name" class="form-control">
                <div v-if="errors.name" class="invalid-feedback">
                  {{errors.name[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">پست الکترونیکی :</div>
            <div class="col-8">
              <input type="text" v-model="edit_admin.email" class="form-control">
                <div v-if="errors.email" class="invalid-feedback">
                  {{errors.email[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">نام کاربری :</div>
            <div class="col-8">
              <input type="text" v-model="edit_admin.user_name" class="form-control">
                <div v-if="errors.user_name" class="invalid-feedback">
                  {{errors.user_name[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">رمز عبور :</div>
            <div class="col-8">
              <input type="text" v-model="edit_admin.password" class="form-control">
                <div v-if="errors.password" class="invalid-feedback">
                  {{errors.password[0]}}
                </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="editAdmin">ویرایش</button>
        </div>
      </div>
    </modal>

    <modal @close="deleteModal = false" :open="deleteModal">
      <div class="card m-b-0">
        <div class="card-header bg-danger">
          <span @click="deleteModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          حذف ادمین
        </div>
        <div class="card-body">
          از حذف ادمین مورد نظر اطمینان دارید؟
        </div>
        <div class="card-footer">
          <button class="btn btn-danger" @click="deleteAdmin">بله</button>
          <button class="btn btn-primary" @click="deleteModal = false">خیر</button>
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
import {TheMask} from 'vue-the-mask'
export default {
  name:'Admins',
  data(){
    return{
      items:[],
      createModal:false,
      editModal:false,
      deleteModal:false,
      delete_admin:null,
      editadmin:null,
      filters:{
        status:2
      },
      admin:{},
      edit_admin:{},
      errors:{},
      current_page:1,
      last_page:1,
    }
  },
  components:{
    Modal,
    TheMask,
  },
  watch:{
    current_page: function (page) {
      this.getUsers(page);
    }
  },
  methods:{
    DeleteModal(id){
      this.delete_admin = id
      this.deleteModal = true
    },
    loadMore(){
      if(this.current_page < this.last_page){
        this.current_page++;
      }
    }, 
    getAdmins(){
      this.SPIN_LOADING(1)
      this.$axios.get(`admins/all`)
      .then(res => {
        this.items = this.items.concat(res.data.data.data)
        this.last_page = res.data.data.last_page;
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        console.log(err);
        this.SPIN_LOADING(0)
      });
    },
    addAdmin(){
      this.SPIN_LOADING(1)
      this.$axios.post(`admins/add` , this.admin)
      .then(res => {
        this.errors = {}
        this.admin = {}
        this.createModal = false
        this.getAdmins()
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    getAdmin(id){
      this.SPIN_LOADING(1)
      this.$axios.get(`admins/get/`+id)
      .then(res => {
        this.errors = {}
        this.edit_admin = res.data.data
        this.editModal = true
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    editAdmin(){
      this.SPIN_LOADING(1)
      this.$axios.post(`admins/edit` , this.edit_admin)
      .then(res => {
        this.errors = {}
        this.edit_admin = {}
        this.getAdmins()
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
    deleteAdmin(){
      this.SPIN_LOADING(1)
      this.$axios.post(`admins/delete` ,{ id:this.delete_admin } )
      .then(res => {
        this.errors = {}
        this.getAdmins()
        this.deleteModal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.deleteModal = false
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    }
  },
  mounted(){
    this.getAdmins()
  }
}
</script>
<style>
</style>
