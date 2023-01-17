<template>
  <div>
    <div class="content-body">
      <div class="content-container">
        <!-- <div class="content-title-green">
          <div class="col-container-3" >
            <div class="right-col" style="width:55%;" >
              <div class="row">
                <img :src="this.StaticUrl+'assets/img/icons/harvest-suc.svg'" alt="" class="user-state-img">
                <p class="user-state-1">برداشت ها</p>
              </div>
            </div>
            <div class="left-col">
              <div class="left-options">
                <input type="text" placeholder="جستجو">
                <div class="img-box-container">
                  <img :src="this.StaticUrl+'assets/img/search_1.png'" alt="">
                </div>
              </div>
            </div>
          </div>
        </div> -->

        <div class="content-heading">
          <div class="row">
            <div class="col-md-4">
              <h3 class="text-light">
                برداشت ها
              </h3>
            </div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <button class="btn btn-secondary" type="button">
                    ترتیب و فیلتر
                  </button>
                </div>
                <select v-model="filters.sort" class="form-control" @change="list = [] , getList()">
                  <option value="newest">جدیدترین ها</option>
                  <option value="oldest">قدیمی ترین ها</option>
                  <option value="confirmed">تایید شده ها</option>
                  <option value="rejected">ردشده ها</option>
                  <option value="pending">بررسی نشده ها</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="col-container-8">
            <table class="table table-sm table-striped table-dark table-bordered">
              <thead>
                <tr>
                  <th>ردیف</th>
                  <th>نام برداشت کننده</th>
                  <th>زمان برداشت</th>
                  <th>مقدار</th>
                  <th>باقیمانده</th>
                  <th>شماره کارت</th>
                  <th>شماره شبا</th>
                  <th>وضعیت</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(item , key) in list" :key="key" class="tbl-row">
                  <th>{{key + 1}}</th>
                  <th>

                    <router-link v-if="item.user.fullname" :to="{name:'User' , params :{ id : item.user.id}}">
                      {{item.user.fullname}}
                    </router-link>
                    <router-link v-else-if="item.user.mobile_number" :to="{name:'User' , params :{ id : item.user.id}}">
                      {{item.user.mobile_number}}
                    </router-link>

                  </th>
                  <th>{{item.registered_at}}</th>
                  <th>{{formatPrice(item.amount)}}</th>
                  <th>{{formatPrice(item.remain)}}</th>
                  <th>{{item.card_number}}</th>
                  <th>{{item.shaba_number}}</th>
                  <th>
                    <template v-if="item.status == 0">
                      <span @click="data.id = item.id , statusModal = true" class="btn btn-sm btn-primary">عملیات</span>
                    </template>
                    <template v-if="item.status == 1">
                      <span :title="item.reject_reason" class="btn btn-sm btn-success">تایید شده</span>
                    </template>
                    <template v-if="item.status == 2">
                      <span :title="item.reject_reason" class="btn btn-sm btn-danger">ردشده</span>
                    </template>
                  </th>
                </tr>
              </tbody>
            </table>
          <div v-if="current_page !== last_page" @click="loadMore" class="btm-box"><p>مشاهده بیشتر</p></div>
        </div>
      </div>
    </div>


    <modal @close="statusModal = false" :open="statusModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="statusModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          تغییر وضعیت
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">انتخاب وضعیت :</div>
            <div class="col-8">
              <select v-model="data.status" class="form-control">
                <option value="1">تایید</option>
                <option value="2">رد کردن</option>
              </select>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">توضیحات</div>
            <div class="col-8">
              <textarea v-model="data.reject_reason" class="form-control"></textarea>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="changeStatus()">تایید</button>
        </div>
      </div>
    </modal>

  </div>
</template>
<script>
import Modal from '../../components/Modal.vue'
export default {
  name:'Harvests',
  components:{
    Modal
  },
  data(){
    return{
      list:[],
      filters:{
        sort:'newest'
      },
      errors:{
      },
      current_page:1,
      last_page:1,
      statusModal:false,
      data:{
        id:null,
        status:1,
        reject_reason:null
      }
    }
  },
  watch:{
    current_page: function (page) {
      this.getList();
    }
  },
  methods:{
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.post(`harvest/list?page=${this.current_page}` , this.filters)
      .then(res => {
        this.list = this.list.concat(res.data.data.data)
        this.last_page = res.data.data.last_page;
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    loadMore(){
      if(this.current_page < this.last_page){
        this.current_page++;
      }
    },
    Search(e){
      if (e.keyCode === 13) {
        this.current_page = 1
        this.users = []
        this.getList()
      }
    },
    changeStatus(){
      this.SPIN_LOADING(1)
      this.$axios.post(`harvest/operate` , this.data)
      .then(res => {
        this.list = []
        this.current_page = 1
        this.getList()
        this.statusModal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
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
