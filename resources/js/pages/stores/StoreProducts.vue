<template>
  <div>

    <div v-if="change_status_modal" class="dialog">
      <div class="dialog-wrapper">
        <div class="dialog-header p-3">تغییر وضعیت محصول با کد {{ selected_item.id }}</div>

        <div class="dialog-body p-4">

          <div class="flexer">
            <div>شناسه</div>
            <div>{{ selected_item.uuid }}</div>
          </div>
          <div class="flexer">
            <div>نام </div>
            <div>{{ selected_item.name }}</div>
          </div>
          <div class="flexer">
            <div>قیمت</div>
            <bdi>{{ formatPrice(selected_item.price) }} تومان</bdi>
          </div>
          <div class="flexer">
            <div class="pl-3">توضیحات</div>
            <pre>{{ selected_item.description }}</pre>
          </div>
          <div class="flexer">
            <div class="pl-3">موجودی</div>
            <div>{{ selected_item.inventory }}</div>
          </div>
          <div class="flexer">
            <div>تاریخ</div>
            <div>{{ selected_item.date }}</div>
          </div>

          <div>
            <h5>تصاویر محصول</h5>
            <div class="p-images">
              <div v-for="(item , key) in selected_item.files" :key="key">
                <a :href="ImageUrl+item.path" target="_blank">
                  <img :src="ImageUrl+item.path" alt="" class="p-img">
                </a>
              </div>
            </div>
          </div>


          <br><br><br>
          <div class="form-group">
            <label for="exampleFormControlTextarea1">انتخاب وضعیت :</label>
            <select v-model="change_status" class="form-control">
              <option :value="0">در حال بررسی</option>
              <option :value="1">تایید شده</option>
              <option :value="2">رد شده</option>
            </select>
          </div>

        </div>
        <div class="dialog-footer">
          <button @click="ChangeStatus()" class="btn btn-sm btn-success">ثبت</button>
          <button @click="change_status_modal = false" class="btn btn-sm btn-default">بستن</button>
        </div>
      </div>
    </div>

    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            محصولات فروشگاه
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
              <option value="newest">جدیدترین ها</option>
              <option value="oldest">قدیمی ترین ها</option>
              <option value="confirmed">تایید شده ها</option>
              <option value="rejected">ردشده ها</option>
              <option value="pending">درحال بررسی ها</option>
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
              <th>کد</th>
              <th>شناسه</th>
              <th>نام</th>
              <th>وضعیت</th>
              <th>تاریخ</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{key + 1}}</td>
              <td>{{item.id}}</td>
              <td>{{item.uuid}}</td>
              <td>{{item.name}}</td>
              <td>
                <span class="text-warning" v-if="parseFloat(item.status) === 0">بررسی نشده</span>
                <span class="text-success" v-if="parseFloat(item.status) === 1">تایید شده</span>
                <span class="text-danger" v-if="parseFloat(item.status) === 2">رد شده</span>
              </td>
              <td>{{item.date}}</td>
              <td>
                <button class="btn btn-sm btn-success" @click="selected_item = item , change_status_modal = true">
                تغییر وضعیت
              </button>
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
  name:'StoreProducts',
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
      change_status_modal:false,
      change_status:0,
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
      this.$axios.post(`store_products/list?page=${this.current_page}` , this.filters)
      .then(res => {
        this.list = this.list.concat(res.data.data.data)
        this.last_page = res.data.data.last_page;
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    ChangeStatus(){
      this.SPIN_LOADING(1)
      this.$axios.post(`store_products/ChangeStatus` , {product_id:this.selected_item.id , status:this.change_status})
      .then(res => {
        this.list = [];
        this.getList();
        this.change_status_modal = false
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
