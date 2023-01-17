<template>
  <div>
    <div class="content-heading">
      <div class="row">
        <div class="col-md-4">
          <h3 class="text-light">
            پلن ها
          </h3>
        </div>
      </div>
    </div>
    <div class="col-container-8">
        <table class="table table-sm table-striped table-dark table-bordered">
          <thead>
            <tr>
              <th>عنوان</th>
              <th>تخفیف شامل</th>
              <th>اعتبار</th>
              <th>قیمت</th>
              <th>عملیات</th>
            </tr>
          </thead>
            
          <tbody>
            <tr v-for="(item , key) in list" :key="key" class="tbl-row">
              <td>{{item.title}}</td>
              <td>{{item.discount}} %</td>
              <td>
                <template v-if="item.validity">{{item.validity}} ماه</template>
                <template v-else>نامحدود</template>
              </td>
              <td>{{item.price}}</td>
              <td>
                <img @click="getData(item.id)" :src="StaticUrl+'assets/img/icons/edit.svg'" alt="" class="profile-ic">
              </td>
            </tr>
          </tbody>
        </table>
    </div>

    <modal @close="editModal = false" :open="editModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="editModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          ویرایش {{item.title}}
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* تماشای هر تبلیغ  :</div>
            <div class="col-8">
              <input type="text" v-model="item.watch_per_ad" class="form-control">
                <div v-if="errors.watch_per_ad" class="invalid-feedback">
                  {{errors.watch_per_ad[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* تماشای هر تبلیغ زیر مجموعه  :</div>
            <div class="col-8">
              <input type="text" v-model="item.subset_watch_per_ad" class="form-control">
                <div v-if="errors.subset_watch_per_ad" class="invalid-feedback">
                  {{errors.subset_watch_per_ad[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* حداکثر زیر مجموعه  :</div>
            <div class="col-8">
              <input type="text" v-model="item.max_referral" class="form-control">
                <div v-if="errors.max_referral" class="invalid-feedback">
                  {{errors.max_referral[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* مدت زمان اجاره زیر مجموعه (روز) :</div>
            <div class="col-8">
              <input type="text" v-model="item.subset_rent_time" class="form-control">
                <div v-if="errors.subset_rent_time" class="invalid-feedback">
                  {{errors.subset_rent_time[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* مدت زمان برداشت از حساب (روز) :</div>
            <div class="col-8">
              <input type="text" v-model="item.harvest_time" class="form-control">
                <div v-if="errors.harvest_time" class="invalid-feedback">
                  {{errors.harvest_time[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* هزینه هر رفرال به (سکه)  :</div>
            <div class="col-8">
              <input type="text" v-model="item.referral_cost_coin" class="form-control">
                <div v-if="errors.referral_cost_coin" class="invalid-feedback">
                  {{errors.referral_cost_coin[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* هزینه هر رفرال (تومان)  :</div>
            <div class="col-8">
              <input type="text" v-model="item.referral_cost_cash" class="form-control">
                <div v-if="errors.referral_cost_cash" class="invalid-feedback">
                  {{errors.referral_cost_cash[0]}}
                </div>
            </div>
          </div>
          <hr>
          <br>
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-4 text-left">* سکه آفر  :</div>
              <div class="col-8">
                <input type="text" v-model="item.offer_coin" class="form-control">
                  <div v-if="errors.offer_coin" class="invalid-feedback">
                    {{errors.offer_coin[0]}}
                  </div>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-4 text-left">* قیمت آفر (تومان)  :</div>
              <div class="col-8">
                <input type="text" v-model="item.offer_cost" class="form-control">
                  <div v-if="errors.offer_cost" class="invalid-feedback">
                    {{errors.offer_cost[0]}}
                  </div>
              </div>
            </div>
          </div>
          <br>
          <hr>
          <div class="row mb-2">
            <div class="col-4 text-left">* تخفیف شامل (درصد) :</div>
            <div class="col-8">
              <input type="text" v-model="item.discount" class="form-control">
                <div v-if="errors.discount" class="invalid-feedback">
                  {{errors.discount[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* اعتبار پلن (ماه) :</div>
            <div class="col-8">
              <input type="text" v-model="item.validity" class="form-control">
                <div v-if="errors.validity" class="invalid-feedback">
                  {{errors.validity[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">* قیمت (تومان) :</div>
            <div class="col-8">
              <input type="text" v-model="item.price" class="form-control">
                <div v-if="errors.price" class="invalid-feedback">
                  {{errors.price[0]}}
                </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="editItem">ویرایش</button>
        </div>
      </div>
    </modal>
    
  </div>
</template>
<script>
import Modal from '../../components/Modal.vue'
export default {
  name:'Plan',
  components:{
    Modal
  },
  data(){
    return{
      list:{},
      item:{},
      editModal:false,
      errors:{},
    }
  },
  methods:{
    getList(){
      this.SPIN_LOADING(1)
      this.$axios.get(`plans/list`)
      .then(res => {
        this.list = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    getData(id){
      this.SPIN_LOADING(1)
      this.$axios.get(`plans/plan/`+id)
      .then(res => {
        this.item = res.data.data
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
      this.$axios.post(`plans/edit` , this.item)
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
  },
  mounted(){
    this.getList()
  }
}
</script>
<style>
</style>
