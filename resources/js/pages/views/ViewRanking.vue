<template>
  <div>
    <div class="content-heading">
      <div class="d-flex justify-content-between py-3 px-1">
        <h3 class="text-light">
        رتبه بندی بازدید های امروز
        </h3>
        <button class="btn btn-sm btn-primary" @click="toggle()">تغییر چینش</button>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th></th>
              <th>کاربر</th>
              <th>تعداد بازدید</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{key+1}}</td>
              <td>

                <router-link v-if="item.fullname" :to="{name:'User' , params :{ id : item.id}}">
                    {{item.fullname}}
                </router-link>
                <router-link v-else-if="item.mobile_number" :to="{name:'User' , params :{ id : item.id}}">
                    {{item.mobile_number}}
                </router-link>

              </td>
              <td>{{item.total_views}}</td>
            </tr>
          </tbody>
        </table>
    </div>

  </div>
</template>
<script>
export default {
  name:'ViewRanking',
  data(){
    return{
      list:{},
      errors:{},
      sort : 'asc'
    }
  },
  methods:{
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`views/ranking`)
      .then(res => {
        this.list = res.data.data

        const a = this.list.slice(0);
        a.sort(function(a,b) {
            return b.total_views - a.total_views;
        });

        this.list = a;

        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    toggle()
    {
        const a = this.list.slice(0);
        if(this.sort === 'asc')
        {
            a.sort(function(a,b) {
                return a.total_views - b.total_views;
            });
            this.sort = 'desc'
        }else{
            a.sort(function(a,b) {
                return b.total_views - a.total_views;
            });
            this.sort = 'asc'
        }

        this.list = a;
    }
  },
  mounted(){
    this.getList()
  }
}
</script>
<style>
</style>
