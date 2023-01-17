<template>
  <div>
    
    <div v-if="modal" class="dialog">
      <div class="dialog-wrapper">
        <div class="dialog-header"></div>
        <div class="dialog-body p-4">
          
        <div class="form-group">
          <label for="exampleFormControlTextarea1">علت رد :</label>
          <textarea v-model="reject_reason" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>
        </div>
        <div class="dialog-footer">
          <button @click="OperateItem(data.id , 2)" class="btn btn-sm btn-success">تایید</button>
          <button @click="modal = false" class="btn btn-sm btn-danger">بستن</button>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="content-container">
        <div class="personal-info-box">
          <div class="title">
            <p>جزییات برداشت</p>
          </div>
          <div v-if="data.status == 0" class="operate">
            <button @click="OperateItem(data.id , 1)" class="btn btn-success"> تایید</button>
            <button @click="modal = true" class="btn btn-danger"> رد</button>
          </div>
          <div v-if="data.status == 1" class="operate">
            <span class="btn btn-success"> تایید شده</span>
          </div>
          <div v-if="data.status == 2" class="operate">
            <span class="btn btn-danger"> رد شده</span>
          </div>
        </div>
        <div class="personal-info-box">
          <div class="content-personal-info-box">
            <div class="row">
              <div class="col-md-6 col-xs-12">
                <p class="py-3 px-5">نام و نام خانوادگی : 
                  <template v-if="data.user">{{data.user.fullname}}</template>
                </p>
                <p class="py-3 px-5">تاریخ ثبت درخواست : {{data.date}}</p>
                <p class="py-3 px-5">ساعت ثبت درخواست : {{data.time}}</p>
                <p v-if="data.status == 2" class="text-danger py-3 px-5">علت رد : {{data.reject_reason}}</p>
              </div>
              <div v-if="data.type == 'crypto'" class="col-md-6 col-xs-12">
                <p class="py-3 px-5">ارز انتخابی : {{data.crypto_symbol}}</p>
                <!-- <p class="py-3 px-5">قیمت ارز : {{data.crypto_price}}</p> -->
                <p class="py-3 px-5">مقدار درخواستی برداشت : {{data.amount}} {{data.crypto_symbol}}</p>
                <p class="py-3 px-5">آدرس کیف پول : {{data.wallet_address}}</p>
              </div>
              <div v-if="data.type == 'rial'" class="col-md-6 col-xs-12">
                <p class="py-3 px-5">شماره کارت : <bdi>{{data.user_bank_account.card_number}}</bdi></p>
                <p class="py-3 px-5">شماره شبا : {{data.user_bank_account.shaba_number}}</p>
                <p class="py-3 px-5">مبلغ درخواستی برداشت : {{data.amount}} تومان</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
import {mapActions} from 'vuex'
export default {
  name:'Deposit',
  data(){
    return{
      data:{},
      modal:false,
      reject_reason:null,
    }
  },
  watch:{
    
  },
  methods:{
    ...mapActions([
      'SPIN_LOADING'
    ]),
    getData(id){
      this.SPIN_LOADING(1)
      this.$axios.get(`harvest/harvest/`+id)
      .then(res => {
        this.data = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    OperateItem(id , status){
      this.SPIN_LOADING(1)
      this.$axios.post(`harvest/operate`,{id:id , status:status , reject_reason:this.reject_reason})
      .then(res => {
        this.getData(this.$route.params.id)
        this.reject_reason = null
        this.modal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.reject_reason = null
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    }
  },
  mounted(){
    if(!this.$route.params.id){
      this.$router.push('/admin/SuccessHarvests');
    }else{
      this.getData(this.$route.params.id)
    }
  }
}
</script>
<style>
</style>
