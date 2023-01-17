<template>
  <div>
    <div class="content-body">
      <div class="content-container">
        <div class="content-heading">
          <div class="row">
            <div class="col-md-4">
              <h3 class="text-danger">
                تیکت های بسته شده
              </h3>
            </div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <button class="btn btn-secondary" type="button" @click="Search">
                    <i class="fa fa-search"></i>
                  </button>
                </div>
                <input v-model="filters.search_key" placeholder="جست و جو" class="form-control" @keyup="enterSearch">
              </div>
            </div>
          </div>
        </div>
        <div class="row mr-0 ml-0 mt-3">
          <div v-for="(item , key) in list" :key="key" class="col-md-4 col-sm-12 p-0 description-box">
            <div class="centered-box">
              <div class="container-boxes">
                <div class="radius-div"></div>
                <div class="top-div-box">{{item.user.fullname}}</div>
                <div class="bottom-div-box">موضوع <small>{{item.subject}}</small></div>
              
              </div>
              <div class="center-div-box">
                <div class="center-div-box-container">
                  <p class="p-top"><small>تاریخ و ساعت: {{item.registered_at}}</small></p>
                  <p class="p-bottom limited"><small>{{item.text}}</small></p>
                </div>
              </div>
              <div class="button-box">
                <div style="margin:0 auto ;">
                  <router-link :to="{name:'Ticket' , params :{ id : item.id}}">
                    <button class="rightbtn">جزییات</button>
                  </router-link>
                </div>
                  
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
export default {
  name:'ClosedTickets',
  data(){
    return{
      list:[],
      filters:{
        search_key:null,
        status:2
      },
      current_page:1,
      last_page:null,
    }
  },
  watch:{
    current_page: function () {
      this.getList();
    }
  },
  methods:{
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.post(`ticket/list?page=${this.current_page}` , this.filters)
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
    Search(){
      this.current_page = 1
      this.list = []
      this.getList()
    }, 
    enterSearch(e){
      if (e.keyCode === 13) {
        this.current_page = 1
        this.list = []
        this.getList()
      }
    }, 
  },
  mounted(){
    this.getList()
  }
}
</script>
<style>
</style>
