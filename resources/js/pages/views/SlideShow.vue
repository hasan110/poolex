<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            اسلاید شو ها
          </h3>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>ردیف</th>
              <th>تصویر</th>
              <th>لینک به</th>
              <th>نوع</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{key+1}}</td>
              <td>
                <a v-if="item.file" target="_blank" :href="ImageUrl+item.file"><img :src="ImageUrl+item.file" alt="" class="profile-im" /></a>
                <template v-else>-</template>
              </td>
              <td>
                <template v-if="item.internal_link == 0">خارج اپ</template>
                <template v-if="item.internal_link == 1">داخل اپ</template>
                <template v-if="item.internal_link == 2">اینستاگرام</template>
                <template v-if="item.internal_link == 3">واتساپ</template>
              </td>
              <td>
                <template v-if="item.type == 'slideshow'">اسلایدشو</template>
                <template v-if="item.type == 'movie_slideshow'">اسلایدشو فیلم ها</template>
                <template v-if="item.type == 'movie_banner'">بنر فیلم</template>
                <template v-if="item.type == 'store_slideshow'">اسلایدشو فروشگاه</template>
              </td>
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
          افزودن اسلایدشو
        </div>
        <div class="card-body">

          <div class="row mb-2">
            <div class="col-4 text-left">
              <label for="formFile" class="form-label">افزودن عکس </label>
              <input ref="file" placeholder="انتخاب تصویر" @change="selectPicture()" class="form-control" type="file" id="formFile">
            </div>
            <div class="col-8">
              <label width="100%" for="formFile" class="form-label">
                <img width="100%" ref="slideshow_file" :src="StaticUrl+'assets/img/image-placeholder.png'" alt="">
              </label>
              <div v-if="errors.file" class="invalid-feedback">
                {{errors.file[0]}}
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">لینک به :</div>
            <div class="col-8 text-right">
              <select v-model="item.internal_link" class="form-control">
                <option :value="0">خارج اپ</option>
                <option :value="1">داخل اپ</option>
                <option :value="2">اینستاگرام</option>
                <option :value="3">واتساپ</option>
              </select>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* لینک  :</div>
            <div class="col-8">
              <select v-if="item.internal_link == 1" v-model="item.link_to" class="form-control">
                <option :value="1">فروشگاه</option>
                <option :value="2">جعبه شانس</option>
              </select>
              <input v-else type="text" v-model="item.link_to" class="form-control form-control-sm">
              <div v-if="errors.link_to" class="invalid-feedback">
                {{errors.link_to[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">نوع :</div>
            <div class="col-8 text-right">
              <select v-model="item.type" class="form-control">
                <option :value="'slideshow'">اسلایدشو</option>
                <option :value="'store_slideshow'">اسلایدشو فروشگاه</option>
                <option :value="'movie_slideshow'">اسلایدشو فیلم ها</option>
                <option :value="'movie_banner'">بنر فیلم</option>
              </select>
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
          ویرایش اسلایدشو
        </div>
        <div class="card-body">

          <div class="mb-3">
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">
              <label for="formFile" class="form-label">
                <template v-if="edit_item.picture">ویرایش تصویر</template>
                <template v-else>افزودن تصویر :</template>
              </label>
              <input ref="edit_file" @change="editPicture()" class="form-control" type="file" id="formFile">
            </div>
            <div class="col-8">
              <label width="100%" for="formFile" class="form-label">
                <img width="100%" ref="slideshow_edit_file" :src="[edit_item.file ? ImageUrl+edit_item.file : StaticUrl+'assets/img/image-placeholder.png']" alt="">
              </label>
            </div>
            <div v-if="errors.new_file" class="invalid-feedback">
              {{errors.new_file[0]}}
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">لینک به :</div>
            <div class="col-8 text-right">
              <select v-model="edit_item.internal_link" class="form-control">
                <option :value="0">خارج اپ</option>
                <option :value="1">داخل اپ</option>
                <option :value="2">اینستاگرام</option>
                <option :value="3">واتساپ</option>
              </select>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* لینک  :</div>
            <div class="col-8">
              <select v-if="edit_item.internal_link == 1" v-model="edit_item.link_to" class="form-control">
                <option :value="1">فروشگاه</option>
                <option :value="2">جعبه شانس</option>
              </select>
              <input v-else type="text" v-model="edit_item.link_to" class="form-control form-control-sm">
              <div v-if="errors.link_to" class="invalid-feedback">
                {{errors.link_to[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">نوع :</div>
            <div class="col-8 text-right">
              <select v-model="edit_item.type" class="form-control">
                <option :value="'slideshow'">اسلایدشو</option>
                <option :value="'store_slideshow'">اسلایدشو فروشگاه</option>
                <option :value="'movie_slideshow'">اسلایدشو فیلم ها</option>
                <option :value="'movie_banner'">بنر فیلم</option>
              </select>
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
  name:'SlideShow',
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
    selectPicture(){
      this.item.file = this.$refs.file.files[0];
      this.$refs.slideshow_file.src = URL.createObjectURL(this.item.file);
    },
    editPicture(){
      this.edit_item.new_file = this.$refs.edit_file.files[0];
      this.$refs.slideshow_edit_file.src = URL.createObjectURL(this.edit_item.new_file);
    },
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`slideshows/list`)
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

      const form = new FormData();

      const d = this.item

      d.link_to ? form.append('link_to', d.link_to) : ''
      d.internal_link ? form.append('internal_link',d.internal_link) : form.append('internal_link',0)
      d.type ? form.append('type', d.type) : ''
      d.file ? form.append('file', d.file) : ''

      this.$axios.post(`slideshows/add` , form)
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
      this.$axios.get(`slideshows/slideshow/`+id)
      .then(res => {
        this.edit_item = res.data.data
        this.errors = {}
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

      const form = new FormData();

      const d = this.edit_item

      d.id ? form.append('id', d.id) : ''
      d.link_to ? form.append('link_to', d.link_to) : ''
      d.type ? form.append('type', d.type) : ''
      d.internal_link ? form.append('internal_link', d.internal_link) : form.append('internal_link', 0)
      d.new_file ? form.append('new_file', d.new_file) : ''

      this.$axios.post(`slideshows/edit` ,form)
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
      this.$axios.post(`slideshows/delete` , {id : id})
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
