<template>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="chart-wrapper">
          <div class="titr">
            <h4>کاربران</h4>
          </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="card text-white bg-primary mb-3">
                    <div class="card-header"><i class="fa fa-users"></i> کل کاربران </div>
                    <div class="card-body">
                        <h1>{{statistics.all_users}}</h1>
                        <div class="text-left text-sm"><router-link :to="{name:'DataChart' , params: {section : 'total_user'}}">جزئیات</router-link></div>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card text-white bg-primary mb-3">
                    <div class="card-header"><i class="fa fa-info-circle"></i> کاربران در حال بررسی</div>
                    <div class="card-body">
                        <h1>{{statistics.pending_users}}</h1>
                        <div class="text-left text-sm">--</div>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card text-white bg-primary mb-3">
                    <div class="card-header"><i class="fa fa-info-circle"></i> کاربران تایید شده</div>
                    <div class="card-body">
                        <h1>{{statistics.confirmed_users}}</h1>
                        <div class="text-left text-sm"><router-link :to="{name:'DataChart' , params: {section : 'confirmed_user'}}">جزئیات</router-link></div>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card text-white bg-primary mb-3">
                    <div class="card-header"><i class="fa fa-key"></i> کلید های کاربران امروز </div>
                    <div class="card-body">
                        <h1>{{statistics.all_keys}}</h1>
                        <div class="text-left text-sm">--</div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <h3 class="text-white mt-4">آمار کلی کاربران</h3>
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <div class="card text-white bg-primary mb-3">
              <div class="card-header"><i class="fa fa-money"></i> موجودی اعتباری کل کاربران</div>
              <div class="card-body">
                <h1>{{statistics.total_cash}}</h1>
                <div class="text-left text-sm"><router-link :to="{name:'DataChart' , params: {section : 'user_cash'}}">جزئیات</router-link></div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="card text-white bg-primary mb-3">
              <div class="card-header"><i class="fa fa-database"></i> موجودی سکه ای کل کاربران</div>
              <div class="card-body">
                <h1>{{statistics.total_coin}}</h1>
                <div class="text-left text-sm"><router-link :to="{name:'DataChart' , params: {section : 'user_coin'}}">جزئیات</router-link></div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card text-white bg-primary mb-3">
              <div class="card-header">کل زیر مجموعه های خریداری شده</div>
              <div class="card-body">
                <h1>{{statistics.allrefs}}</h1>
                <div class="text-left text-sm">--</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 col-sm-12">
        <div class="chart-wrapper">
          <div class="text-light">
            <h4 class="mt-4">بازدید ها</h4>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
                    <div class="card-header">همه بازدید ها</div>
                    <div class="card-body">
                        <h1>{{statistics.all_views}}</h1>
                        <div class="text-left text-sm"><router-link :to="{name:'DataChart' , params: {section : 'view'}}">جزئیات</router-link></div>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
                    <div class="card-header">بازدید های امروز</div>
                    <div class="card-body">
                        <h1>{{statistics.today_watched_ads}}</h1>
                        <div class="text-left text-sm"><router-link :to="{name:'ViewRanking'}">رنکینگ روزانه</router-link></div>
                    </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <h3 class="text-white">تعداد پلن های فعال برای کاربران</h3>
      </div>
      <div class="col-md-4 col-sm-12">
        <div class="card text-white bg-danger mb-3">
          <div class="card-header">پلن برنزی </div>
          <div class="card-body">
            <h1>{{ statistics.bronze_plan }}</h1>
          </div>
        </div>
      </div>
      <div class="col-md-4 col-sm-12">
        <div class="card text-white bg-secondary mb-3">
          <div class="card-header">پلن نقره ای </div>
          <div class="card-body">
            <h1>{{ statistics.silver_plan }}</h1>
          </div>
        </div>
      </div>
      <div class="col-md-4 col-sm-12">
        <div class="card text-white bg-warning mb-3">
          <div class="card-header">پلن طلایی </div>
          <div class="card-body">
            <h1>{{ statistics.gold_plan }}</h1>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
export default {
  name:'Dashboard',
  data(){
    return {
      statistics : {},
    }
  },
  mounted(){
    this.getData()
  },
  methods:{

    getData(){
      this.SPIN_LOADING(1)
      this.$axios.get(`dashboard_details`)
      .then(res => {
        this.statistics = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    }
  }
}
</script>
<style>
</style>
