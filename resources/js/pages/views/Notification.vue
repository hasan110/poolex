<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            نوتیفیکیشن
          </h3>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>عنوان</th>
              <th>متن</th>
              <th>وضعیت ارسال</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{item.title}}</td>
              <td>{{item.body}}</td>
              <td>
                <template v-if="item.status_send">
                  ارسال شده
                </template>
                <template v-else>
                 درانتظارارسال
                </template>
              </td>
              <td>
                <button v-if="!item.status_send" @click="Send(item.id)" class="btn btn-sm btn-primary">ارسال</button>
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
            <div class="col-4 text-left">* عنوان  :</div>
            <div class="col-8">
              <input type="text" v-model="item.title" class="form-control">
                <div v-if="errors.title" class="invalid-feedback">
                  {{errors.title[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* متن  :</div>
            <div class="col-8">
              <textarea type="text" v-model="item.body" class="form-control"></textarea>
                <div v-if="errors.body" class="invalid-feedback">
                  {{errors.body[0]}}
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
  name:'Notification',
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
      this.$axios.post(`notifications/list`)
      .then(res => {
        this.list = res.data.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    Create(){
      this.SPIN_LOADING(1)
      this.$axios.post(`notifications/create` , this.item)
      .then(res => {
        this.getList()
        this.createModal = false
        this.item = {}
        this.$alert(res.data.message , "موفق" , "success");
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    Send(id){
      this.SPIN_LOADING(1)
      this.$axios.post(`notifications/send` , {id : id})
      .then(res => {
        this.getList()
        this.SPIN_LOADING(0)
        this.$alert(res.data.message , "موفق" , "success");
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
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
