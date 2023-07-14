<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            فروشندگان سایت
          </h3>
        </div>
        <div class="col-md-4">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <button class="btn btn-secondary" type="button">
                ترتیب و فیلتر
              </button>
            </div>
            <select v-model="filters.sort" class="form-control" @change="users = [] , getUsers()">
              <option value="newest">جدیدترین ها</option>
              <option value="oldest">قدیمی ترین ها</option>
              <option value="has_income">درآمد دارها</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <button class="btn btn-secondary" type="button" @click="Search">
                <i class="fa fa-search"></i>
              </button>
            </div>
            <input v-model="filters.search_key" placeholder="جست و جو" class="form-control" @keyup="Search">
          </div>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>ردیف</th>
              <th>نام</th>
              <th>شماره تلفن</th>
              <th>درآمد</th>
              <th>اطلاعات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(user , key) in users" :key="key" class="tbl-row">
              <td>{{key + 1}}</td>
              <td>{{user.fullname}}</td>
              <td>{{user.mobile_number}}</td>
              <td>{{user.income}}</td>
              <td>
                <router-link :to="{name:'User' , params :{ id : user.id}}">
                  <i style="font-size:18px;color:#fff" class="fa fa-user"></i>
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      <div v-if="current_page !== last_page" @click="loadMore" class="btm-box"><p>مشاهده بیشتر</p></div>
    </div>
  </div>
</template>
<script>
export default {
  name:'Users',
  data(){
    return{
      users:[],
      filters:{
        is_seller:1,
        search_key:'',
        sort:'newest'
      },
      current_page:1,
      last_page:1,
    }
  },
  watch:{
    current_page: function (page) {
      this.getUsers();
    }
  },
  methods:{
    getUsers(){
      this.SPIN_LOADING(1)
      this.$axios.post(`users/list?page=${this.current_page}` , this.filters)
      .then(res => {
        this.users = this.users.concat(res.data.data.data)
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
        this.getUsers()
      }
    },
  },
  mounted(){
    this.getUsers()
  }
}
</script>
<style>
</style>
