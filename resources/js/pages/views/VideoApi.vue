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
        <div class="col-md-8">
            <button class="btn btn-success" @click="moveFilms()">انتقال همه فیلم ها</button>
            <button v-if="selected_series.length" class="btn btn-success" @click="selected_series_modal = true">انتقال قسمت سریال</button>
        </div>
        <div class="col-md-4">
        </div>
      </div>
    </div>

    <div class="col-container-8">
      <div class="table-responsive">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th>عنوان فیلم</th>
              <th>پوستر</th>
              <th>فایل</th>
              <th>نوع</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>
                  <input type="checkbox" v-model="selected_series" :value="item.mid">
              </td>
              <td>{{key+1}}</td>
              <td>{{item.title}}</td>
              <td>
                <template v-if="item.poster">
                  <a v-if="item.poster" target="_blank" :href="item.poster"><img :src="item.poster" class="profile-im" /></a>
                </template>
              </td>
              <td>
                <template v-if="item.quality">
                  <a v-if="item.quality" target="_blank" :href="item.quality.Q_720p">
                    فایل
                  </a>
                </template>
                <template v-else>---</template>
              </td>
              <td>{{item.type}}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


    <modal @close="selected_series_modal = false" :open="selected_series_modal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="selected_series_modal = false" class="close-modal"><i class="fa fa-times"></i></span>
          انتقال سریال های انتخاب شده
        </div>
        <div class="card-body">

          <div class="row mb-2">
            <div class="col-4 text-left">انتخاب سریال:</div>
            <div class="col-8">
              <select v-model="move_to_series.video_id" class="form-control">
                <option v-for="(item , key) in serials" :key="key" :value="item.id">{{ item.title }}</option>
              </select>
              <div v-if="errors.serial_id" class="invalid-feedback">
                {{errors.serial_id[0]}}
              </div>
            </div>
          </div>

        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="moveSeries()">انتقال</button>
        </div>
      </div>
    </modal>


  </div>
</template>
<script>
import Modal from '../../components/Modal.vue'
export default {
  name:'VideoApi',
  components:{
    Modal
  },
  data(){
    return{
      list:[],
      serials:{},
      categories:{},
      genres:{},
      move_to_series:{},
      selected_series:[],
      selected_series_modal:false,
      loading:false,
      errors:{},
      edit_errors:{},
    }
  },
  methods:{
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`video-api/list`)
      .then(res => {
        this.list = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.$alert(err.response.data.message , "ناموفق" , "error");
        this.SPIN_LOADING(0)
      });
    },
    getSerials(){
      this.SPIN_LOADING(1)
      this.$axios.get(`videos/serial_list`)
      .then(res => {
        this.serials = res.data.data
        console.log(this.serials);
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
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
    moveFilms(){
      this.SPIN_LOADING(1)
      this.$axios.get(`video-api/move-films`)
      .then(res => {
        this.$alert(res.data.message , "موفق" , "success");
        this.getList()
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.$alert(err.response.data.message , "ناموفق" , "error");
        this.SPIN_LOADING(0)
      });
    },
    moveSeries(){
      this.SPIN_LOADING(1)
      this.move_to_series.videos = this.selected_series.join(',')
      this.$axios.post(`video-api/move-series` , this.move_to_series)
      .then(res => {
        this.$alert("سریال های جدید با موفقیت منتقل شدند." , "موفق" , "success");
        this.getList()
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.$alert(err.response.data.message , "ناموفق" , "error");
        this.SPIN_LOADING(0)
      });
    },
  },
  mounted(){
    this.getList()
    this.getSerials()
    // this.getCategories()
    // this.getGenres()
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
