<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            فروشگاه
          </h3>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>قیمت به تومان</th>
              <th>تعداد سکه</th>
              <th>تعداد سکه قبل تخفیف</th>
              <th>عملیات</th>
            </tr>
          </thead>
            
          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{item.price}}</td>
              <td>{{item.coin}}</td>
              <td>{{item.before_coin}}</td>
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
          افزودن محصول
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* قیمت محصول به تومان  :</div>
            <div class="col-8">
              <input type="number" v-model="item.price" class="form-control">
                <div v-if="errors.price" class="invalid-feedback">
                  {{errors.price[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* سکه های دریافتی  :</div>
            <div class="col-8">
              <input type="number" v-model="item.coin" class="form-control">
                <div v-if="errors.coin" class="invalid-feedback">
                  {{errors.coin[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* سکه های دریافتی قبل از تخفیف  :</div>
            <div class="col-8">
              <input type="number" v-model="item.before_coin" class="form-control">
                <div v-if="errors.before_coin" class="invalid-feedback">
                  {{errors.before_coin[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left"> اولویت اول؟ :</div>
            <div class="">
              <input type="checkbox" value="1" v-model="item.priority" class="form-control">
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
          ویرایش محصول
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* قیمت محصول به تومان  :</div>
            <div class="col-8">
              <input type="number" v-model="edit_item.price" class="form-control">
                <div v-if="errors.price" class="invalid-feedback">
                  {{errors.price[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* سکه های دریافتی  :</div>
            <div class="col-8">
              <input type="number" v-model="edit_item.coin" class="form-control">
                <div v-if="errors.coin" class="invalid-feedback">
                  {{errors.coin[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* سکه های دریافتی قبل از تخفیف  :</div>
            <div class="col-8">
              <input type="number" v-model="edit_item.before_coin" class="form-control">
                <div v-if="errors.before_coin" class="invalid-feedback">
                  {{errors.before_coin[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left"> اولویت اول؟ :</div>
            <div class="">
              <input type="checkbox" value="1" v-model="edit_item.priority" class="form-control">
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
  name:'Store',
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
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`products/list`)
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
      this.$axios.post(`products/add` , this.item)
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
      this.$axios.get(`products/product/`+id)
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
      this.$axios.post(`products/edit` , this.edit_item)
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
      this.$axios.post(`products/delete` , {id : id})
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
