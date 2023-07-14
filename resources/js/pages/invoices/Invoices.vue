<template>
  <div>

      <div v-if="details_modal" class="dialog">
          <div class="dialog-wrapper">
              <div class="dialog-header p-3">سفارش با کد {{ selected_item.id }}</div>

              <div class="dialog-body p-4">

                  <div class="flexer">
                      <h5>اطلاعات کلی</h5>
                      <div></div>
                  </div>
                  <div class="flexer">
                      <div>شناسه سفارش</div>
                      <div>{{ selected_item.uuid }}</div>
                  </div>
                  <div class="flexer">
                      <div>وضعیت سفارش</div>
                      <div>{{ selected_item.status_title }}</div>
                  </div>
                  <div class="flexer">
                      <div>تاریخ</div>
                      <div>{{ selected_item.date }}</div>
                  </div>


                  <div class="flexer">
                      <h5>هزینه ها</h5>
                      <div></div>
                  </div>
                  <div class="flexer">
                      <div>مبلغ فاکتور</div>
                      <bdi>{{ formatPrice(selected_item.invoice_amount) }} تومان</bdi>
                  </div>
                  <div class="flexer">
                      <div>هزینه ارسال</div>
                      <div>
                          <template v-if="selected_item.shipping_cost">
                              {{ formatPrice(selected_item.shipping_cost) }} تومان
                          </template>
                          <template v-else>رایگان</template>
                      </div>
                  </div>
                  <div class="flexer">
                      <div>جمع فاکتور</div>
                      <bdi>{{ formatPrice(selected_item.total_amount) }} تومان</bdi>
                  </div>

                  <div class="flexer">
                      <h5>اطلاعات خریدار</h5>
                      <div></div>
                  </div>
                  <div class="flexer">
                      <div>نام و نام خانوادگی</div>
                      <div>{{ selected_item.fullname }}</div>
                  </div>
                  <div class="flexer">
                      <div>استان</div>
                      <div>{{ selected_item.province }}</div>
                  </div>
                  <div class="flexer">
                      <div>شهر</div>
                      <div>{{ selected_item.city }}</div>
                  </div>
                  <div class="flexer">
                      <div>کد پستی</div>
                      <div>{{ selected_item.postal_code }}</div>
                  </div>
                  <div class="flexer">
                      <div class="pl-3">آدرس خریدار</div>
                      <pre>{{ selected_item.address }}</pre>
                  </div>
                  <div class="flexer">
                      <div class="pl-3">شماره تماس</div>
                      <bdi v-if="selected_item.call_number">
                          <a :href="'tel:'+selected_item.call_number">{{ selected_item.call_number }}</a>
                      </bdi>
                  </div>
                  <div class="flexer">
                      <h5>اطلاعات فروشنده</h5>
                      <div></div>
                  </div>
                  <div class="flexer">
                      <div>نام و نام خانوادگی</div>
                      <div v-if="selected_item.seller">{{ selected_item.seller.fullname }}</div>
                  </div>
                  <div class="flexer">
                      <div>نام فروشگاه</div>
                      <div v-if="selected_item.store">{{ selected_item.store.name }}</div>
                  </div>
                  <div class="flexer">
                      <div>شماره تماس فروشنده</div>
                      <bdi v-if="selected_item.seller">
                          <a :href="'tel:'+selected_item.seller.mobile_number">{{ selected_item.seller.mobile_number }}</a>
                      </bdi>
                  </div>
                  <div class="flexer">
                      <div>آدرس فروشگاه</div>
                      <div v-if="selected_item.store">{{ selected_item.store.address }}</div>
                  </div>

                  <div>
                      <h6 class="mt-4">محصولات سفارش</h6>
                      <table class="table table-sm table-striped table-bordered">
                          <thead>
                          <tr>
                              <th>ردیف</th>
                              <th>نام محصول</th>
                              <th>تعداد</th>
                              <th>قیمت</th>
                          </tr>
                          </thead>

                          <tbody>
                          <tr v-for="(item , key) in selected_item.items" :key="key" class="tbl-row">
                              <td>{{key + 1}}</td>
                              <td>{{item.store_product.name}}</td>
                              <td>{{item.count}}</td>
                              <td>{{formatPrice(item.cost)}} تومان</td>
                          </tr>
                          </tbody>
                      </table>
                  </div>

              </div>
              <div class="dialog-footer">
                  <button @click="details_modal = false" class="btn btn-sm btn-default">بستن</button>
              </div>
          </div>
      </div>


    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            سفارشات
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
              <option value="invoice_amount">قیمت محصولات فاکتور</option>
                <option value="total_amount">قیمت کل فاکتور</option>
                <option value="newest">جدیدترین ها</option>
                <option value="oldest">قدیمی ترین ها</option>
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
              <th>نام گیرنده</th>
              <th>شماره تلفن</th>
              <th>استان</th>
              <th>شهر</th>
              <th>وضعیت</th>
              <th>تاریخ</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{key + 1}}</td>
              <td>{{item.fullname}}</td>
              <td>{{item.call_number}}</td>
              <td>{{item.province}}</td>
              <td>{{item.city}}</td>
              <td>{{item.status_title}}</td>
              <td>{{item.date}}</td>
              <td>
                  <button v-if="item.status == 3" class="btn btn-sm btn-success" @click="Operate(item.id , 4)">ارسال سفارش</button>
                  <button class="btn btn-sm btn-primary" @click="selected_item = item , details_modal = true">جزییات</button>
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
  name:'Invoices',
  data(){
    return{
      list:[],
      filters:{
        search_key:'',
        sort:'newest'
      },
      current_page:1,
      last_page:1,
      details_modal:false,
    }
  },
  watch:{
    current_page: function (page) {
      this.getList();
    }
  },
  methods:{
    getList(reset = false){
      if(reset)
      {
        this.list = [];
        this.last_page = 1;
        this.current_page = 1;
      }
      this.SPIN_LOADING(1)
      this.$axios.post(`invoices/list?page=${this.current_page}` , this.filters)
      .then(res => {
        this.list = this.list.concat(res.data.data.data)
        this.last_page = res.data.data.last_page;
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    Operate(id , status){
      this.SPIN_LOADING(1)
      this.$axios.post(`invoices/operate` , {id , status})
      .then(res => {
        this.getList(true);
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
