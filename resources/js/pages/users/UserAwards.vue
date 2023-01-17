<template>
  <div>
    <div class="content-body">
      <div class="content-container">
        <div class="content-heading">
          <div class="row">
            <div class="col-md-4">
              <h3 class="text-light">
                جوایز کاربران
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
                  <th>کاربر</th>
                  <th>اطلاعات جایزه</th>
                  <th>شماره تلفن</th>
                  <th>وضعیت</th>
                  <th>تاریخ</th>
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
                  <th>
                    <template v-if="item.award">
                    {{item.award.title}}
                    </template>
                  </th>
                  <th>{{item.number}}</th>
                  <th>
                    <template v-if="item.status == 0">
                      <span @click="data.id = item.id , statusModal = true" class="btn btn-sm btn-primary">عملیات</span>
                    </template>
                    <template v-if="item.status == 1">
                      <span class="btn btn-sm btn-success">تایید شده</span>
                    </template>
                    <template v-if="item.status == 2">
                      <span class="btn btn-sm btn-danger">ردشده</span>
                    </template>
                  </th>
                  <th>{{item.registered_at}}</th>
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
          <!-- <div class="row mb-2">
            <div class="col-4 text-left">توضیحات</div>
            <div class="col-8">
              <textarea v-model="data.reject_reason" class="form-control"></textarea>
            </div>
          </div> -->
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
  name:'UserAwards',
  components:{
    Modal
  },
  data(){
    return{
      list:[],
      filters:{
        sort:'newest'
      },
      current_page:1,
      last_page:1,
      statusModal:false,
      data:{
        id:null,
        status:1
      }
    }
  },
  watch:{
    current_page: function (page) {
      this.getList(page);
    }
  },
  methods:{
    getList(page){
      this.SPIN_LOADING(1)
      this.$axios.post(`user_awards/list?page=${page}` , this.filters)
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
        this.getList(this.current_page)
      }
    },
    changeStatus(){
      this.SPIN_LOADING(1)
      this.$axios.post(`user_awards/operate` , this.data)
      .then(res => {
        this.list = []
        this.current_page = 1
        this.getList(this.current_page)
        this.statusModal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
  },
  mounted(){
    this.getList(this.current_page)
  }
}
</script>
<style>
</style>
