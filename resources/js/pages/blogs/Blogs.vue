<template>
  <div>
    <modal @close="delete_modal = false" :open="delete_modal">
        <div class="card">
            <div class="card-body">
                از حذف بلاگ
                ({{selected_item.title}})
                اطمینان دارید؟
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-danger" @click="deleteBlog()">
                    حذف
                </button>
                <button class="btn btn-sm btn-default" @click="delete_modal = false">
                    انصراف
                </button>
            </div>
        </div>
    </modal>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            بلاگ ها
          </h3>
        </div>
        <div class="col-md-4">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <button class="btn btn-secondary" type="button">
                ترتیب و فیلتر
              </button>
            </div>
            <select v-model="filters.sort" class="form-control" @change="list = [] , getList()">
              <option value="newest">جدید ترین ها</option>
              <option value="oldest">قدیمی ترین ها</option>
              <option value="favorites">پر بازدید ترین ها</option>
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
              <th>شناسه</th>
              <th>تصویر</th>
              <th>عنوان</th>
              <th>تاریخ</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{key + 1}}</td>
              <td>{{item.uuid}}</td>
              <td>
                  <a v-if="item.thumbnail" :href="ImageUrl + item.thumbnail" target="_blank">
                      <img :src="ImageUrl + item.thumbnail" class="profile-im">
                  </a>
                  <span v-else>ندارد</span>
              </td>
              <td>{{item.title}}</td>
              <td>{{item.date}}</td>
              <td>
                <button class="btn btn-sm btn-danger" @click="selected_item = item , delete_modal = true">
                  حذف
                </button>
                <router-link :to="{name:'EditBlog' , params:{id:item.uuid}}" class="btn btn-sm btn-primary">
                  ویرایش
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
import Modal from '../../components/Modal.vue'

export default {
  name:'Blogs',
  components:{
    Modal
  },
  data(){
    return{
      list:[],
      filters:{
        search_key:'',
        sort:'newest'
      },
      current_page:1,
      last_page:1,
      selected_item:{},
      delete_modal:false
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
      this.$axios.post(`blogs/list?page=${this.current_page}` , this.filters)
      .then(res => {
        this.list = this.list.concat(res.data.data.data)
        this.last_page = res.data.data.last_page;
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    deleteBlog(){
      this.SPIN_LOADING(1)
      this.$axios.post(`blogs/delete` , {blog_id:this.selected_item.uuid })
      .then( () => {
        this.list = [];
        this.getList();
        this.delete_modal = false
      })
      .catch( () => {
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
.flexer{
  display: flex;
  border-bottom: 1px solid #e7e7e7;
  align-items: center;
  justify-content: space-between;
  padding: .25rem;
  font-size: 14px;
}
.p-images{
  display: flex;
  align-items: center;
  justify-content: flex-start;
  flex-wrap: wrap;
}
.p-images .p-img{
  padding: .25rem;
}
.p-images img{
  width: 100px;
  height: 100px;
  border-radius: 4px;
}
</style>
