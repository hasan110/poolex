<template>
  <div>
      <div class="container-fluid" style="max-width: 800px;">
          <h3 class="text-white p-3">ثبت بلاگ</h3>
          <div class="row">
              <div class="col-md-6 col-sm-12">
                  <div class="form-group text-white">
                      <label for="title">عنوان</label>
                      <input v-model="blog.title" class="form-control" id="title">
                      <small v-if="errors.title" class="invalid-feedback">
                          {{errors.title[0]}}
                      </small>
                  </div>
              </div>
              <div class="col-md-6 col-sm-12">
                  <div class="form-group text-white">
                      <label for="blog_keywords">کلمات کلیدی</label>
                      <input v-model="blog.blog_keywords" placeholder="کلمات را با ویرگول از هم جدا کنید." class="form-control" id="blog_keywords">
                      <small v-if="errors.blog_keywords" class="invalid-feedback">
                          {{errors.blog_keywords[0]}}
                      </small>
                  </div>
              </div>
              <div class="col-md-6 col-sm-12">
                  <div class="form-group text-white">
                      <label for="thumbnail">تصویر بند انگشتی</label>
                      <div class="custom-file">
                          <input ref="thumbnail" type="file" class="custom-file-input" id="thumbnail" @change="fileSelected('thumbnail')">
                          <label class="custom-file-label" for="thumbnail">انتخاب فایل</label>
                      </div>
                      <small v-if="errors.thumbnail" class="invalid-feedback">
                          {{errors.thumbnail[0]}}
                      </small>
                      <div v-if="thumbnail_preview" class="p-1">
                          <img style="height: 100px;width: auto;object-fit: contain;" :src="thumbnail_preview">
                      </div>
                  </div>
              </div>
              <div class="col-md-6 col-sm-12">
                  <div class="form-group text-white">
                      <label for="banner">بنر</label>
                      <div class="custom-file">
                          <input ref="banner" type="file" class="custom-file-input" id="banner" @change="fileSelected('banner')">
                          <label class="custom-file-label" for="banner">انتخاب فایل</label>
                      </div>
                      <small v-if="errors.banner" class="invalid-feedback">
                          {{errors.banner[0]}}
                      </small>
                      <div v-if="banner_preview" class="p-1">
                          <img style="height: 100px;width: auto;object-fit: contain;" :src="banner_preview">
                      </div>
                  </div>
              </div>
              <div class="col-md-12 col-sm-12">
                  <div class="form-group text-white">
                      <label for="short_description">توضیح کوتاه</label>
                      <textarea rows="4" v-model="blog.short_description" class="form-control" id="short_description"></textarea>
                      <small v-if="errors.short_description" class="invalid-feedback">
                          {{errors.short_description[0]}}
                      </small>
                  </div>
              </div>
              <div class="col-md-12 col-sm-12">
                  <div class="form-group text-white">
                      <label for="short_description">متن مقاله</label>
                      <vue-editor v-model="blog.blog_content" />
                      <small v-if="errors.blog_content" class="invalid-feedback">
                          {{errors.blog_content[0]}}
                      </small>
                  </div>
              </div>
          </div>
          <button class="btn btn-success my-3" @click="addBlog()">ثبت</button>
      </div>
  </div>
</template>
<script>
import { VueEditor } from "vue2-editor";
export default {
  name:'CreateBlog',
  components:{
    VueEditor
  },
  data(){
    return{
      blog:{},
      errors:{},
      thumbnail_preview:null,
      banner_preview:null,
    }
  },
  methods:{
    fileSelected(type){
        if(type === 'banner'){
            this.blog.banner = this.$refs.banner.files[0];
            this.banner_preview = URL.createObjectURL(this.blog.banner);
        }
        else if(type === 'thumbnail'){
            this.blog.thumbnail = this.$refs.thumbnail.files[0];
            this.thumbnail_preview = URL.createObjectURL(this.blog.thumbnail);
        }
    },
    addBlog(){
      this.SPIN_LOADING(1)

      const form = new FormData;

      for (const [key, value] of Object.entries(this.blog)) {
          form.append(key , value);
      }

      this.$axios.post(`blogs/add` , form,
      {
          headers: {
              'Content-Type': 'multipart/form-data'
          }
      })
      .then( () => {
          this.$router.push({name:'Blogs'});
      })
      .catch( err => {
          const error = err.response.data
          if(error.errors){ this.errors = error.errors }
          else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      })
      .finally(() => {
          this.SPIN_LOADING(0)
      });
    },
  }
}
</script>
<style>
.quillWrapper{
    background: #fff;
}
.ql-editor{
    color:#000;
    font-family: IRANSansBold !important;
}
.ql-header{
    text-align:end;
}
</style>
