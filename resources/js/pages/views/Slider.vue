<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            اسلاید های راهنمایی
          </h3>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>متن</th>
              <th>تاریخ ایجاد</th>
              <th>تاریخ ویرایش</th>
              <th>عملیات</th>
            </tr>
          </thead>
            
          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{item.description}}</td>
              <td>{{item.create}}</td>
              <td>{{item.update}}</td>
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
          افزودن 
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* متن  :</div>
            <div class="col-8">
              <textarea type="text" v-model="item.description" class="form-control"></textarea>
                <div v-if="errors.description" class="invalid-feedback">
                  {{errors.description[0]}}
                </div>
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
          ویرایش
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* متن  :</div>
            <div class="col-8">
              <textarea type="text" v-model="edit_item.description" class="form-control"></textarea>
                <div v-if="errors.description" class="invalid-feedback">
                  {{errors.description[0]}}
                </div>
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
  name:'Help',
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
      this.$axios.get(`posts/list/2`)
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
      this.item.type = 2
      this.$axios.post(`posts/add` , this.item)
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
      this.$axios.get(`posts/post/`+id)
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
      this.edit_item.type = 2
      this.$axios.post(`posts/edit` , this.edit_item)
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
      this.$axios.post(`posts/delete` , {id : id})
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
