<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            دسته بندی ها
          </h3>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>ردیف</th>
              <th>عنوان</th>
              <th>اولویت</th>
              <th>نوع</th>
              <th>تاریخ</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{key+1}}</td>
              <td>{{item.title}}</td>
              <td>{{item.priority}}</td>
              <td>
                  <template v-if="item.type === 'category'">
                      دسته بندی
                  </template>
                  <template v-if="item.type === 'store_category'">
                      دسته بندی فروشگاه
                  </template>
                  <template v-else>
                      ژانر
                  </template>
              </td>
              <td>{{item.registered_at}}</td>
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
            <div class="col-4 text-left">* عنوان:</div>
            <div class="col-8">
              <input v-model="item.title" class="form-control">
                <div v-if="errors.title" class="invalid-feedback">
                  {{errors.title[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* اولویت:</div>
            <div class="col-8">
              <input v-model="item.priority" class="form-control">
                <div v-if="errors.priority" class="invalid-feedback">
                  {{errors.priority[0]}}
                </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">نوع :</div>
            <div class="col-8 text-right">
              <select v-model="item.type" class="form-control">
                <option :value="'category'">دسته بندی</option>
                <option :value="'store_category'">دسته بندی فروشگاه</option>
                <option :value="'genre'">ژانر</option>
              </select>
            </div>
          </div>

            <div class="row mb-2">
                <div class="col-4 text-left">* رنگ  :</div>
                <div class="col-8">
                    <input type="color" v-model="item.color" class="form-control">
                    <div v-if="errors.color" class="invalid-feedback">
                        {{errors.color[0]}}
                    </div>
                </div>
            </div>

          <div class="row mb-2">
            <div class="col-4 text-left">انتخاب آیکون :</div>
            <div class="col-8 text-right">
              <input type="file" @change="file_selected()" ref="app_file" class="form-control form-control-sm">
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
            <div class="col-4 text-left">* عنوان  :</div>
            <div class="col-8">
              <input v-model="edit_item.title" class="form-control">
                <div v-if="errors.title" class="invalid-feedback">
                  {{errors.title[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* اولویت:</div>
            <div class="col-8">
              <input v-model="edit_item.priority" class="form-control">
                <div v-if="errors.priority" class="invalid-feedback">
                  {{errors.priority[0]}}
                </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">نوع :</div>
            <div class="col-8 text-right">
              <select v-model="edit_item.type" class="form-control">
                <option :value="'category'">دسته بندی</option>
                <option :value="'store_category'">دسته بندی فروشگاه</option>
                <option :value="'genre'">ژانر</option>
              </select>
            </div>
          </div>

            <div class="row mb-2">
                <div class="col-4 text-left">* رنگ  :</div>
                <div class="col-8">
                    <input type="color" v-model="edit_item.color" class="form-control">
                    <div v-if="errors.color" class="invalid-feedback">
                        {{errors.color[0]}}
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-4 text-left">ویرایش آیکون :</div>
                <div class="col-8 text-right">
                    <input type="file" @change="edit_file_selected()" ref="edit_app_file" class="form-control form-control-sm">
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
  name:'Category',
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
    file_selected()
    {
        this.item.icon = this.$refs.app_file.files[0];
    },
    edit_file_selected()
    {
        this.edit_item.new_icon = this.$refs.edit_app_file.files[0];
    },
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`categories/list`)
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
      const form = new FormData;
      for (const [key, value] of Object.entries(this.item)) {
        if(value || value == 0)
        {
          form.append(key , value);
        }
      }

      this.$axios.post(`categories/add` , form)
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
      this.$axios.get(`categories/category/`+id)
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
        const form = new FormData;
        for (const [key, value] of Object.entries(this.edit_item)) {
            if(value || value == 0)
            {
                form.append(key , value);
            }
        }
      this.$axios.post(`categories/edit` , form)
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
      this.$axios.post(`categories/delete` , {id : id})
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
