<template>
  <div>

    <div v-if="loading" class="lds-wrapper">
      <div class="lds-ring">
        <div></div><div></div><div></div><div></div>
        <br>
        <br>
        <br>
        <br>
      </div>
      <div v-if="uploadPercentage" class="prog">
        <progress max="100" :value="uploadPercentage"></progress>
        <span>{{uploadPercentage}}%</span>
      </div>
    </div>

    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            فیلم و سریال
          </h3>
        </div>
        <div class="col-md-4">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <button class="btn btn-secondary" type="button">
                ترتیب و فیلتر
              </button>
            </div>
            <select v-model="filters.sort" class="form-control" @change="current_page = 1 , list = [] , getList()">
              <option value="name">عنوان</option>
              <option value="newest">جدیدترین ها</option>
              <option value="oldest">قدیمی ترین ها</option>
              <option value="film">فیلم ها</option>
              <option value="series">سریال ها</option>
              <option value="episode">قسمت های سریال</option>
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
            <input v-model="filters.search_key" placeholder="جست و جو" class="form-control" @keyup="enterSearch">
          </div>
        </div>
      </div>
    </div>

    <div class="col-container-8">
      <div class="table-responsive">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th></th>
              <th>ID</th>
              <th>عنوان فیلم</th>
              <th>پوستر</th>
              <th>فایل</th>
              <th>نوع</th>
              <th>تاریخ</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{key+1}}</td>
              <td>{{item.id}}</td>
              <td>{{item.title}}</td>
              <td>
                <template v-if="item.poster">
                    <template v-if="item.complete_link == 2">
                        <a target="_blank" :href="item.poster"><img :src="item.poster" class="profile-im" /></a>
                    </template>
                    <template v-else>
                        <a target="_blank" :href="ImageUrl+item.poster"><img :src="ImageUrl+item.poster" class="profile-im" /></a>
                    </template>
                </template>
              </td>
              <td>
                <template v-if="item.file">
                    <template v-if="item.complete_link == 0">
                        <a v-if="item.file" target="_blank" :href="ImageUrl+item.file">
                            فایل
                        </a>
                    </template>
                    <template v-else>
                        <a v-if="item.file" target="_blank" :href="item.file">
                            فایل
                        </a>
                    </template>
                </template>
                <template v-else>---</template>
              </td>
              <td>
                <template v-if="item.type == 1">فیلم</template>
                <template v-if="item.type == 2">سریال</template>
                <template v-if="item.type == 3">
                  قسمتی از سریال
                  <template v-if="item.serial">
                    {{item.serial.title}}
                  </template>
                </template>
                <template v-if="item.type == 4">تریلر</template>
              </td>
              <td>{{item.create}}</td>
              <td>
                <button @click="getData(item.id)" class="btn btn-sm btn-primary">ویرایش</button>
                <button @click="deleteItem(item.id)" class="btn btn-sm btn-danger">حذف</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="current_page !== last_page" @click="loadMore" class="btm-box"><p>مشاهده بیشتر</p></div>
      </div>
    </div>

    <modal @close="createModal = false" :open="createModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="createModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          افزودن فیلم
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* عنوان  :</div>
            <div class="col-8">
              <input v-model="item.title" class="form-control">
              <div v-if="errors.title" class="invalid-feedback">
                {{errors.title[0]}}
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">نوع:</div>
            <div class="col-8">
              <select v-model="item.type" class="form-control">
                <option value="1">فیلم</option>
                <option value="2">سریال</option>
                <option value="3">قسمتی از سریال</option>
                <option value="4">تریلر</option>
              </select>
              <div v-if="errors.type" class="invalid-feedback">
                {{errors.type[0]}}
              </div>
            </div>
          </div>
          <div v-if="item.type == 3" class="row mb-2">
            <div class="col-4 text-left">انتخاب سریال:</div>
            <div class="col-8">
              <select v-model="item.video_id" class="form-control">
                <option v-for="(item , key) in serials" :key="key" :value="item.id">{{ item.title }}</option>
              </select>
              <div v-if="errors.serial_id" class="invalid-feedback">
                {{errors.serial_id[0]}}
              </div>
            </div>
          </div>
          <div v-if="item.type == 3" class="row mb-2">
            <div class="col-4 text-left">شماره قسمت:</div>
            <div class="col-8">
              <input v-model="item.episode_number" class="form-control">
              <div v-if="errors.episode_number" class="invalid-feedback">
                {{errors.episode_number[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">دسته بندی:</div>
            <div class="col-8">
              <select v-model="item.categories" class="form-control" multiple>
                <option v-for="(item , key) in categories" :key="key" :value="item.id">{{item.title}}</option>
              </select>
              <div v-if="errors.categories" class="invalid-feedback">
                {{errors.categories[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">ژانر:</div>
            <div class="col-8">
              <select v-model="item.genres" class="form-control" multiple>
                <option v-for="(item , key) in genres" :key="key" :value="item.id">{{item.title}}</option>
              </select>
              <div v-if="errors.genres" class="invalid-feedback">
                {{errors.genres[0]}}
              </div>
            </div>
          </div>

          <div v-if="item.type == 1 || item.type == 2 || item.type == 4" class="row mb-2">
            <div class="col-4 text-left">
              <label for="formFile" class="form-label">پوستر</label>
              <input ref="picture" placeholder="انتخاب تصویر" @change="selectPicture()" class="hide" type="file" id="formFile">
            </div>
            <div class="col-8">
              <label width="100%" for="formFile" class="form-label">
                <img width="100%" ref="ad_picture" :src="StaticUrl+'assets/img/image-placeholder.png'" alt="">
              </label>
              <div v-if="errors.poster" class="invalid-feedback">
                {{errors.poster[0]}}
              </div>
            </div>
          </div>
          <template v-if="item.type == 1 || item.type == 3 || item.type == 4">
            <div class="row mb-2">
                <div class="col-4 text-left">* آدرس فایل  :</div>
                <div class="col-8">
                <input v-model="item.file" class="form-control">
                <div v-if="errors.file" class="invalid-feedback">
                    {{errors.file[0]}}
                </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4 text-left">آدرس دهی فایل ها  :</div>
                <div class="col-8">
                    <select v-model="item.complete_link" class="form-control">
                        <option value="0">داخلی</option>
                        <option value="1">فایل بیرونی</option>
                        <option value="2">فایل و پوستر بیرونی</option>
                    </select>
                    <div v-if="errors.complete_link" class="invalid-feedback">
                        {{errors.complete_link[0]}}
                    </div>
                </div>
            </div>
          </template>

          <div class="row mb-2">
            <div class="col-4 text-left">وضعیت ترجمه:</div>
            <div class="col-8">
              <select v-model="item.translate_status" class="form-control">
                <option value="1">دوبله فارسی</option>
                <option value="2">زیرنویس فارسی</option>
                <option value="3">زبان اصلی</option>
              </select>
              <div v-if="errors.translate_status" class="invalid-feedback">
                {{errors.translate_status[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">امتیاز:</div>
            <div class="col-8">
              <input v-model="item.rate" class="form-control">
              <div v-if="edit_errors.rate" class="invalid-feedback">
                {{edit_errors.rate[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* توضیحات  :</div>
            <div class="col-8">
              <textarea type="text" rows="10" v-model="item.description" class="form-control form-control-sm"></textarea>
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
          ویرایش فیلم
        </div>
        <div class="card-body">

          <div class="row mb-2">
            <div class="col-4 text-left">* عنوان  :</div>
            <div class="col-8">
              <input v-model="edit_item.title" class="form-control">
              <div v-if="edit_errors.title" class="invalid-feedback">
                {{edit_errors.title[0]}}
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">نوع:</div>
            <div class="col-8">
              <select disabled v-model="edit_item.type" class="form-control">
                <option value="1">فیلم</option>
                <option value="2">سریال</option>
                <option value="3">قسمتی از سریال</option>
                <option value="4">تریلر</option>
              </select>
              <div v-if="edit_errors.type" class="invalid-feedback">
                {{edit_errors.type[0]}}
              </div>
            </div>
          </div>
          <div v-if="edit_item.type == 3" class="row mb-2">
            <div class="col-4 text-left">انتخاب سریال:</div>
            <div class="col-8">
              <select v-model="edit_item.video_id" class="form-control">
                <option v-for="(item , key) in serials" :key="key" :value="item.id">{{ item.title }}</option>
              </select>
              <div v-if="edit_errors.serial_id" class="invalid-feedback">
                {{edit_errors.serial_id[0]}}
              </div>
            </div>
          </div>
          <div v-if="edit_item.type == 3" class="row mb-2">
            <div class="col-4 text-left">شماره قسمت:</div>
            <div class="col-8">
              <input v-model="edit_item.episode_number" class="form-control">
              <div v-if="edit_errors.episode_number" class="invalid-feedback">
                {{edit_errors.episode_number[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">دسته بندی:</div>
            <div class="col-8">
              <select v-model="edit_item.edit_categories" class="form-control" multiple>
                <option v-for="(item , key) in categories" :key="key" :value="item.id">{{item.title}}</option>
              </select>
              <div v-if="errors.categories" class="invalid-feedback">
                {{errors.categories[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">ژانر:</div>
            <div class="col-8">
              <select v-model="edit_item.edit_genres" class="form-control" multiple>
                <option v-for="(item , key) in genres" :key="key" :value="item.id">{{item.title}}</option>
              </select>
              <div v-if="errors.genres" class="invalid-feedback">
                {{errors.genres[0]}}
              </div>
            </div>
          </div>

          <div v-if="edit_item.type == 1 || edit_item.type == 2 || edit_item.type == 4" class="row mb-2">
            <div class="col-4 text-left">
              <label for="formFile" class="form-label">
                <template v-if="edit_item.poster">ویرایش پوستر</template>
                <template v-else>افزودن پوستر :</template>
              </label>
              <input ref="edit_picture" @change="editPicture()" class="form-control" type="file" id="formFile">
            </div>
            <div class="col-8">
              <label width="100%" for="formFile" class="form-label">
                <img width="100%" ref="ad_edit_picture" :src="[edit_item.poster ? ImageUrl+edit_item.poster : StaticUrl+'assets/img/image-placeholder.png']" alt="">
              </label>
            </div>
            <div v-if="edit_errors.new_poster" class="invalid-feedback">
              {{edit_errors.new_poster[0]}}
            </div>
          </div>
          <template v-if="edit_item.type == 1 || edit_item.type == 3 || edit_item.type == 4">
            <div class="row mb-2">
                <div class="col-4 text-left">* آدرس فایل  :</div>
                <div class="col-8">
                <input v-model="edit_item.file" class="form-control">
                <div v-if="edit_errors.file" class="invalid-feedback">
                    {{edit_errors.file[0]}}
                </div>
                </div>
            </div>
            <div class="row mb-2">

                <div class="col-4 text-left">آدرس دهی فایل ها:</div>
                <div class="col-8">
                    <select v-model="edit_item.complete_link" class="form-control">
                        <option value="0">داخلی</option>
                        <option value="1">فایل بیرونی</option>
                        <option value="2">فایل و پوستر بیرونی</option>
                    </select>
                    <div v-if="edit_errors.complete_link" class="invalid-feedback">
                        {{edit_errors.complete_link[0]}}
                    </div>
                </div>
            </div>
          </template>

          <div class="row mb-2">
            <div class="col-4 text-left">وضعیت ترجمه:</div>
            <div class="col-8">
              <select v-model="edit_item.translate_status" class="form-control">
                <option value="1">دوبله فارسی</option>
                <option value="2">زیرنویس فارسی</option>
                <option value="3">زبان اصلی</option>
              </select>
              <div v-if="errors.translate_status" class="invalid-feedback">
                {{errors.translate_status[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">امتیاز:</div>
            <div class="col-8">
              <input v-model="edit_item.rate" class="form-control">
              <div v-if="edit_errors.rate" class="invalid-feedback">
                {{edit_errors.rate[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* توضیحات  :</div>
            <div class="col-8">
              <textarea type="text" rows="10" v-model="edit_item.description" class="form-control form-control-sm"></textarea>
              <div v-if="edit_errors.description" class="invalid-feedback">
                {{edit_errors.description[0]}}
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
  name:'Store',
  components:{
    Modal
  },
  data(){
    return{
      list:[],
      serials:{},
      categories:{},
      genres:{},
      item:{},
      edit_item:{},
      createModal:false,
      editModal:false,
      loading:false,
      errors:{},
      edit_errors:{},
      uploadPercentage:0,
      filters:{
        search_key:null,
        sort:'newest'
      },
      current_page:1,
      last_page:1,
    }
  },
  watch:{
    current_page: function (page) {
      this.getList();
    }
  },
  methods:{
    selectPicture(){
      this.item.poster = this.$refs.picture.files[0];
      this.$refs.ad_picture.src = URL.createObjectURL(this.item.poster);
    },
    selectVideo(){
      this.item.file = this.$refs.video.files[0];
    },
    editPicture(){
      this.edit_item.new_poster = this.$refs.edit_picture.files[0];
      this.$refs.ad_edit_picture.src = URL.createObjectURL(this.edit_item.new_poster);
    },
    editVideo(){
      this.edit_item.new_file = this.$refs.edit_video.files[0];
    },
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.post(`videos/list?page=${this.current_page}`,this.filters)
      .then(res => {
        this.list = this.list.concat(res.data.data.data)
        this.last_page = res.data.data.last_page;
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    getSerials(){
      this.SPIN_LOADING(1)
      this.$axios.get(`videos/serial_list`)
      .then(res => {
        this.serials = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    Create(){
      this.loading = true

      const form = new FormData();

      const d = this.item

      d.title ? form.append('title', d.title) : ''
      d.video_id ? form.append('video_id', d.video_id) : ''
      d.poster ? form.append('poster', d.poster) : ''
      d.file ? form.append('file', d.file) : ''
      d.type ? form.append('type', d.type) : ''
      d.rate ? form.append('rate', d.rate) : ''
      d.translate_status ? form.append('translate_status', d.translate_status) : ''
      d.episode_number ? form.append('episode_number', d.episode_number) : ''
      d.description ? form.append('description', d.description) : ''
      d.complete_link ? form.append('complete_link', d.complete_link) : form.append('complete_link', 0)
      d.categories ? form.append('categories', d.categories) : ''
      d.genres ? form.append('genres', d.genres) : ''

      this.$axios.post(`videos/add` , form , {
        onUploadProgress: function( progressEvent ) {
          this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
        }.bind(this)
      })
      .then(res => {
        this.list = []
        this.getList()
        this.getSerials()
        this.createModal = false
        this.item = {}
        this.loading = false
      })
      .catch(err => {
        this.loading = false
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    getData(id){
      this.SPIN_LOADING(1)
      this.$axios.get(`videos/video/`+id)
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

      const form = new FormData();

      const d = this.edit_item

      d.id ? form.append('id', d.id) : ''
      d.title ? form.append('title', d.title) : ''
      d.video_id ? form.append('video_id', d.video_id) : ''
      d.new_poster ? form.append('new_poster', d.new_poster) : ''
      d.file ? form.append('file', d.file) : ''
      d.type ? form.append('type', d.type) : ''
      d.rate ? form.append('rate', d.rate) : ''
      d.translate_status ? form.append('translate_status', d.translate_status) : ''
      d.episode_number ? form.append('episode_number', d.episode_number) : ''
      d.description ? form.append('description', d.description) : ''
      d.complete_link ? form.append('complete_link', d.complete_link) : form.append('complete_link', 0)
      d.edit_categories ? form.append('categories', d.edit_categories) : ''
      d.edit_genres ? form.append('genres', d.edit_genres) : ''

      this.$axios.post(`videos/edit` , form)
      .then(res => {
        this.list = []
        this.getList()
        this.editModal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.edit_errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    deleteItem(id){
      this.SPIN_LOADING(1)
      this.$axios.post(`videos/delete` , {id : id})
      .then(res => {
        this.list = []
        this.getList()
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    getCategories(){
      this.SPIN_LOADING(1)
      this.$axios.get(`categories/list?type=category`)
      .then(res => {
        this.categories = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    getGenres(){
      this.SPIN_LOADING(1)
      this.$axios.get(`categories/list?type=genre`)
      .then(res => {
        this.genres = res.data.data
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
    enterSearch(e){
      if (e.keyCode === 13) {
        this.current_page = 1
        this.list = []
        this.getList()
      }
    },
    Search(){
      this.current_page = 1
      this.list = []
      this.getList()
    },
  },
  mounted(){
    this.getList()
    this.getSerials()
    this.getCategories()
    this.getGenres()
  }
}
</script>
<style>
.prog{
  position: absolute;
left: 50%;
transform: translate(-50% , 230%);
}
</style>
