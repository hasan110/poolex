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
    <div class="row text-light">
      <div class="col-md-4">
        <h3>
          تبلیغات
        </h3>
      </div>
      <div class="col-md-4"></div>
      <div class="col-md-4 text-left">
        <button @click="renewModal = true" class="btn btn-sm btn-success">تمدید همه</button>
      </div>
    </div>
  </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>عنوان</th>
              <th>تصویر</th>
              <th>ویدئو</th>
              <th>شماره تماس</th>
              <th>آیدی اینستا</th>
              <th>لینک</th>
              <th>توضیحات</th>
              <th>عملیات</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{item.title}}</td>
              <td>
                <a v-if="item.picture" target="_blank" :href="ImageUrl+item.picture"><img :src="ImageUrl+item.picture" alt="" class="profile-im" /></a>
                <template v-else>-</template>
              </td>
              <td>
                <a v-if="item.video" target="_blank" class="btn btn-primary btn-sm" :href="ImageUrl+item.video">تماشا</a>
                <template v-else>ندارد</template>
              </td>
              <td>{{item.number}}</td>
              <td>{{item.instagram_id}}</td>
              <td>{{item.link}}</td>
              <td>{{item.description}}</td>
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
          افزودن تبلیغ
        </div>
        <div class="card-body">

          <div class="row mb-2">
            <div class="col-4 text-left">
              <label for="formFile" class="form-label">افزودن عکس تبلیغ</label>
              <input ref="picture" placeholder="انتخاب تصویر" @change="selectPicture()" class="form-control" type="file" id="formFile">
            </div>
            <div class="col-8">
              <label width="100%" for="formFile" class="form-label">
                <img width="100%" ref="ad_picture" :src="StaticUrl+'assets/img/image-placeholder.png'" alt="">
              </label>
              <div v-if="errors.picture" class="invalid-feedback">
                {{errors.picture[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* عنوان  :</div>
            <div class="col-8">
              <input type="text" v-model="item.title" class="form-control form-control-sm">
                <div v-if="errors.title" class="invalid-feedback">
                  {{errors.title[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* شماره تبلیغ کننده  :</div>
            <div class="col-8">
              <input type="text" v-model="item.number" class="form-control form-control-sm">
                <div v-if="errors.number" class="invalid-feedback">
                  {{errors.number[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* آیدی اینستاگرام  :</div>
            <div class="col-8">
              <input type="text" v-model="item.instagram_id" class="form-control form-control-sm">
                <div v-if="errors.instagram_id" class="invalid-feedback">
                  {{errors.instagram_id[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* لینک  :</div>
            <div class="col-8">
              <input type="text" v-model="item.link" class="form-control form-control-sm">
                <div v-if="errors.link" class="invalid-feedback">
                  {{errors.link[0]}}
                </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">نوع:</div>
            <div class="col-8">
              <select v-model="item.type" class="form-control">
                <option value="1">ادموب</option>
                <option value="2">تپسل</option>
                <option value="3">ادیوری</option>
                <option value="4">فروشگاه</option>
              </select>
              <div v-if="errors.type" class="invalid-feedback">
                {{errors.type[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">تبلیغ دوم:</div>
            <div class="col-8">
              <select v-model="item.second_ad" class="form-control">
                <option value="1">همسان</option>
                <option value="2">بنر</option>
              </select>
              <div v-if="errors.second_ad" class="invalid-feedback">
                {{errors.second_ad[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* توضیحات  :</div>
            <div class="col-8">
              <textarea type="text" rows="5" v-model="item.description" class="form-control form-control-sm"></textarea>
                <div v-if="errors.description" class="invalid-feedback">
                  {{errors.description[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">دریافت کل سکه با فالو:</div>
            <div class="col-8 text-right">
              <input type="checkbox" v-model="item.two_step" class="form-control form-control-sm">
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">افزودن آدرس ویدئو  :</div>
            <div class="col-8">
              <input placeholder="آدرس ویدئو را وارد کنید" class="form-control form-control-sm" v-model="item.video">
              <!-- <input ref="video" placeholder="انتخاب تصویر" @change="selectVideo()" class="form-control form-control-sm" id="formFileSm" type="file"> -->
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
          ویرایش تبلیغ
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
              <input ref="edit_picture" @change="editPicture()" class="form-control" type="file" id="formFile">
            </div>
            <div class="col-8">
              <label width="100%" for="formFile" class="form-label">
                <img width="100%" ref="ad_edit_picture" :src="[edit_item.picture ? ImageUrl+edit_item.picture : StaticUrl+'assets/img/image-placeholder.png']" alt="">
              </label>
            </div>
            <div v-if="errors.new_picture" class="invalid-feedback">
              {{errors.new_picture[0]}}
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* عنوان  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_item.title" class="form-control form-control-sm">
                <div v-if="errors.title" class="invalid-feedback">
                  {{errors.title[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* شماره تبلیغ کننده  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_item.number" class="form-control form-control-sm">
                <div v-if="errors.number" class="invalid-feedback">
                  {{errors.number[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* آیدی اینستاگرام  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_item.instagram_id" class="form-control form-control-sm">
                <div v-if="errors.instagram_id" class="invalid-feedback">
                  {{errors.instagram_id[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* لینک  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_item.link" class="form-control form-control-sm">
                <div v-if="errors.link" class="invalid-feedback">
                  {{errors.link[0]}}
                </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">نوع:</div>
            <div class="col-8">
              <select v-model="edit_item.type" class="form-control">
                <option value="1">ادموب</option>
                <option value="2">تپسل</option>
                <option value="3">ادیوری</option>
                <option value="4">فروشگاه</option>
              </select>
              <div v-if="errors.type" class="invalid-feedback">
                {{errors.type[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">تبلیغ دوم:</div>
            <div class="col-8">
              <select v-model="edit_item.second_ad" class="form-control">
                <option value="1">همسان</option>
                <option value="2">بنر</option>
              </select>
              <div v-if="errors.second_ad" class="invalid-feedback">
                {{errors.second_ad[0]}}
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">* توضیحات  :</div>
            <div class="col-8">
              <textarea type="text" rows="5" v-model="edit_item.description" class="form-control form-control-sm"></textarea>
                <div v-if="errors.description" class="invalid-feedback">
                  {{errors.description[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">دریافت کل سکه با فالو:</div>
            <div class="col-8">
              <input type="checkbox" v-model="edit_item.two_step" class="form-control form-control-sm">
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">
              <template v-if="edit_item.video">ویرایش آدرس ویدئو</template>
              <template v-else>افزودن آدرس ویدئو :</template>
            </div>
            <div class="col-8">
              <!-- <input ref="edit_video" @change="editVideo()" class="form-control form-control-sm" id="formFileSm" type="file"> -->
              <input placeholder="آدرس ویدئو را وارد کنید" class="form-control form-control-sm" v-model="edit_item.video">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="editItem">ویرایش</button>
        </div>
      </div>
    </modal>

    <modal @close="renewModal = false" :open="renewModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="renewModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          تمدید تبلیغات
        </div>
        <div class="card-body">
            آیا از تمدید تبلیغات به همراه صفر شدن کلیدها اطمینان خاطر دارید؟
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="renewAll()">ادامه</button>
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
  name:'Advertise',
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
      renewModal:false,
      loading:false,
      uploadPercentage:0,
      errors:{},
    }
  },
  methods:{
    selectPicture(){
      this.item.picture = this.$refs.picture.files[0];
      this.$refs.ad_picture.src = URL.createObjectURL(this.item.picture);
    },
    selectVideo(){
      this.item.video = this.$refs.video.files[0];
    },
    editPicture(){
      this.edit_item.new_picture = this.$refs.edit_picture.files[0];
      this.$refs.ad_edit_picture.src = URL.createObjectURL(this.edit_item.new_picture);
    },
    editVideo(){
      this.edit_item.new_video = this.$refs.edit_video.files[0];
    },
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`advertises/list`)
      .then(res => {
        this.list = res.data.data
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
      d.number ? form.append('number', d.number) : ''
      d.instagram_id ? form.append('instagram_id', d.instagram_id) : ''
      d.link ? form.append('link', d.link) : ''
      d.description ? form.append('description', d.description) : ''
      d.two_step ? form.append('two_step', d.two_step) : ''
      d.type ? form.append('type', d.type) : ''
      d.second_ad ? form.append('second_ad', d.second_ad) : form.append('second_ad', 1)
      d.picture ? form.append('picture', d.picture) : ''
      d.video ? form.append('video', d.video) : ''

      this.$axios.post(`advertises/add` , form , {
        onUploadProgress: function( progressEvent ) {
          this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
        }.bind(this)
      })
      .then(res => {
        this.getList()
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
      this.$axios.get(`advertises/advertise/`+id)
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
      this.loading = true

      const form = new FormData();

      const d = this.edit_item

      d.id ? form.append('id', d.id) : ''
      d.title ? form.append('title', d.title) : ''
      d.number ? form.append('number', d.number) : ''
      d.instagram_id ? form.append('instagram_id', d.instagram_id) : ''
      d.link ? form.append('link', d.link) : ''
      d.description ? form.append('description', d.description) : ''
      d.two_step ? form.append('two_step', d.two_step) : ''
      d.type ? form.append('type', d.type) : ''
      d.new_picture ? form.append('new_picture', d.new_picture) : ''
      d.second_ad ? form.append('second_ad', d.second_ad) : form.append('second_ad', 1)
      // d.new_video ? form.append('new_video', d.new_video) : ''
      d.video ? form.append('video', d.video) : ''

      this.$axios.post(`advertises/edit` ,form , {
        onUploadProgress: function( progressEvent ) {
          this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
        }.bind(this)
      })
      .then(res => {
        this.getList()
        this.editModal = false
        this.loading = false
      })
      .catch(err => {
        this.loading = false
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    deleteItem(id){
      this.SPIN_LOADING(1)
      this.$axios.post(`advertises/delete` , {id : id})
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
    renewAll(){
      this.SPIN_LOADING(1)
      this.$axios.get(`advertises/renew_all`)
      .then( () => {
        this.$alert("همه تبلیغات با موفقیت تمدید شدند" , "موفق" , "success");
        this.renewModal = false
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
