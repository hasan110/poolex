<template>
  <div class="content-body gray">
    <div class="content-container">

      <div v-if="user_modal" class="dialog">
        <div class="dialog-wrapper">
          <div class="dialog-header"></div>
          <div class="dialog-body p-4">

          <div class="form-group">
            <label for="exampleFormControlTextarea1">انتخاب وضعیت :</label>
            <select v-model="change_status" class="form-control">
              <option value="1">در حال بررسی</option>
              <option value="2">تایید شده</option>
              <option value="3">رد شده</option>
            </select>
          </div>
          <div v-if="change_status == 3" class="form-group">
            <label for="exampleFormControlTextarea1">علت رد :</label>
            <textarea v-model="reject_reason" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
          </div>
          </div>
          <div class="dialog-footer">
            <button @click="OperateUser(user.id , change_status)" class="btn btn-sm btn-success">تایید</button>
            <button @click="user_modal = false" class="btn btn-sm btn-danger">بستن</button>
          </div>
        </div>
      </div>

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
            <button @click="Operate(account_id , 0)" class="btn btn-sm btn-success">تایید</button>
            <button @click="modal = false" class="btn btn-sm btn-danger">بستن</button>
          </div>
        </div>
      </div>


      <!-- <div class="img-profile-box">
        <img :src="this.StaticUrl+'assets/img/empty-user.png'" alt="">
      </div> -->
      <div class="personal-info-box">
        <div class="title">
          <p>اطلاعات هویتی</p>
        </div>
        <div class="operate">
          <span @click="editModal = true" class="btn btn-primary mx-1"> ویرایش</span>
        </div>
        <div class="operate">
          <button @click="user_modal = true" class="btn btn-info"> تغییر وضعیت</button>
        </div>
        <div v-if="user.status == 0" class="operate">
          <span class="btn btn-outline-secondary mx-1"> ثبت نام اولیه</span>
        </div>
        <div v-if="user.status == 1" class="operate">
          <span class="btn btn-outline-warning mx-1"> در حال بررسی</span>
        </div>
        <div v-if="user.status == 2" class="operate">
          <span class="btn btn-outline-success mx-1"> تایید شده</span>
        </div>
        <div v-if="user.status == 3" class="operate">
          <span class="btn btn-outline-danger mx-1"> رد شده</span>
        </div>
        <div class="operate" @click="userCharge = !userCharge">
          <span class="btn btn-outline-warning mx-1">شارژ</span>
        </div>
        <div class="operate" @click="giveAwardModal = !giveAwardModal">
          <span class="btn btn-outline-secondary mx-1">اهدای جعبه شانس</span>
        </div>
        <div class="operate" @click="giveReferalModal = !giveReferalModal">
          <span class="btn btn-outline-primary mx-1">اهدای رفرال</span>
        </div>
        <div v-if="parseInt(user.is_seller) && user.income > 0" class="operate" @click="checkoutSeller()">
          <span class="btn btn-success mx-1">تسویه فروشنده</span>
        </div>
      </div>
      <div class="personal-info-box">
        <div class="content-personal-info-box">
          <div class="row" v-if="user.plan">
            پلن : {{user.plan.title}} --- اعتبار :
            <template v-if="user.user_plan.expire">
              {{user.user_plan.expire}}
            </template>
            <template v-else>
              نامحدود
            </template>
          </div>
          <div class="row">
            <div class="custom-col-sm-4 custom-col-md-4 custom-col-bs-4">
              <p>نام و نام خانوادگی : {{user.fullname}}</p>
              <p>تلفن همراه : {{user.mobile_number}}</p>
              <p> نام کاربری : {{user.username}}</p>
              <p> ایمیل : {{user.email}}</p>
              <p> ایمیل : {{user.email}}</p>
            </div>
            <div class="custom-col-sm-4 custom-col-md-4 custom-col-bs-4">
              <p>تاریخ تولد : {{user.birth_date}}</p>
              <p>محل تولد : {{user.birth_place}}</p>
              <p> کد معرف این کاربر : {{user.identifier_code}}</p>
            </div>
            <div class="custom-col-sm-4 custom-col-md-4 custom-col-bs-4">

            </div>
            <div v-if="user.status == 3" class="row m-4">
              <div class="text-danger">علت رد :{{user.reject_reason}}</div>
            </div>
          </div>
          <div class="row m-4">
            تعداد سکه : {{user.coins}} ----- موجودی : {{user.cash}}
          </div>
          <div v-if="parseInt(user.is_seller)" class="row m-4">
            درآمد : {{user.income}}
          </div>
          <div class="row m-4">
            تعداد کل بازدید ها : {{user.all_views}} ----- تعداد بازدید های امروز : {{user.today_views}}
          </div>
          <div class="row m-4">
            تعداد کل برداشت ها : {{user.harvests_count}}
          </div>
        </div>
      </div>


      <div class="personal-info-box">
        <p class="p-3" style="margin-top:5px">اطلاعات بانکی</p>
      </div>
      <div class="personal-info-box">
        <div class="content-personal-info-box">
          <div class="container p-3">
            <div class="row">
              <div v-if="user.bank_account" class="col-md-6 col-sm-12 bank-card">
                <p> تاریخ و ساعت : {{user.bank_account.shamsi_created_at}}
                  <span v-if="user.bank_account.card_image" class="float-left">
                    <a target="_blank" download :href="ImageUrl+user.bank_account.card_image" class="btn btn-primary btn-sm">نمایش تصویر</a>
                  </span>
                </p>
                <p>شماره حساب : {{user.bank_account.account_number}}</p>
                <p>شماره شبا : IR{{user.bank_account.shaba_number}}</p>
                <p>شماره کارت :
                  <bdi>{{user.bank_account.card_number}}</bdi>
                </p>
                <div v-if="user.bank_account.status == 0" class="bankcard-operate">
                  <button @click="Operate(user.bank_account.id , 1)" class="operate-btn confirm">تایید</button>
                  <button @click="ToggleModal(user.bank_account.id)" class="operate-btn reject">رد</button>
                </div>
                <div v-if="user.bank_account.status == 1" class="bankcard-operate">
                  <div class="confirmed">تایید شده <i class="fa fa-check"></i></div>
                </div>
              </div>
              <div v-else>
                <div class="text-default">
                  اطلاعات کارت بانکی وارد نشده است.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <br>
      <div class="personal-info-box">
        <h4 class="p-2 mb-0" >رفرال ها</h4>
      </div>
      <div class="personal-info-box">
        <div class="content-personal-info-box">
          <div class="container p-3">
            <div v-if="!Object.keys(user.user_referrals).length" class="text-center text-muted">رفرالی یافت نشد.</div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover table-sm font-sm">
                <thead>
                  <tr>
                    <th></th>
                    <th>رفرال</th>
                    <th>بازدید های امروز</th>
                    <th>تاریخ انقضا</th>
                    <th>تاریخ ثبت</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item , key) in user.user_referrals" :key="key">
                    <th>{{key + 1}}</th>
                    <th>
                      <template v-if="item.referral">
                        <router-link :to="{name:'User' , params :{ id : item.referral.id}}">
                          {{item.referral.id}} - {{item.referral.fullname}}
                        </router-link>
                      </template>
                      <template v-else>
                        ---
                      </template>
                    </th>
                    <th>{{item.ref_today_views}}</th>
                    <th>{{item.expire}}</th>
                    <th>{{item.create}}</th>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>

    <modal @close="editModal = false" :open="editModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="editModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          ویرایش کاربر
        </div>
        <div class="card-body">
          <div class="row mb-2">
              <div class="col-4 text-left">* نام  :</div>
            <div class="col-8">
              <input type="text" v-model="edit_user.fullname" class="form-control">
                <div v-if="errors.fullname" class="invalid-feedback">
                  {{errors.fullname[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">* تاریخ تولد :</div>
            <div class="col-8">
              <the-mask :masked="true" v-model="edit_user.birth_date" class="form-control" id="account_number" :mask="[
                '##/##/####'
              ]" />
                <div v-if="errors.birth_date" class="invalid-feedback">
                  {{errors.birth_date[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">* کد ملی :</div>
            <div class="col-8">
              <the-mask :masked="true" v-model="edit_user.national_code" class="form-control" id="account_number" :mask="[
                '#########################'
              ]" />
                <div v-if="errors.national_code" class="invalid-feedback">
                  {{errors.national_code[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">* محل تولد :</div>
            <div class="col-8">
              <input type="text" v-model="edit_user.birth_place" class="form-control">
                <div v-if="errors.birth_place" class="invalid-feedback">
                  {{errors.birth_place[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">* آدرس :</div>
            <div class="col-8">
              <input type="text" v-model="edit_user.address" class="form-control">
                <div v-if="errors.address" class="invalid-feedback">
                  {{errors.address[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">* ایمیل :</div>
            <div class="col-8">
              <input type="text" v-model="edit_user.email" class="form-control">
                <div v-if="errors.email" class="invalid-feedback">
                  {{errors.email[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">شماره ثابت :</div>
            <div class="col-8">
              <input type="text" v-model="edit_user.phone_number" class="form-control">
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">کد معرف :</div>
            <div class="col-8">
              <input type="text" v-model="edit_user.identifier_code" class="form-control">
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">فروشنده است؟ :</div>
            <div class="col-8">
              <input type="checkbox" v-model="edit_user.is_seller" class="form-control">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="editUser">ویرایش</button>
        </div>
      </div>
    </modal>

    <modal @close="userCharge = false" :open="userCharge">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="userCharge = false" class="close-modal"><i class="fa fa-times"></i></span>
          شارژ حساب کاربر
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* شارژ به  :</div>
            <div class="col-8">
              <select v-model="charge.to" class="form-control">
                <option :value="1">سکه</option>
                <option :value="0">موجودی</option>
              </select>
                <div v-if="charge_errors.to" class="invalid-feedback">
                  {{charge_errors.to[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">* نوع  :</div>
            <div class="col-8">
              <select v-model="charge.type" class="form-control">
                <option :value="1">افزایش</option>
                <option :value="0">کاهش</option>
              </select>
                <div v-if="charge_errors.type" class="invalid-feedback">
                  {{charge_errors.type[0]}}
                </div>
            </div>
          </div>
          <div class="row mb-2">
              <div class="col-4 text-left">* مقدار :</div>
            <div class="col-8">
              <input @keypress="just_float($event)" v-model="charge.amount" class="form-control">
              <div v-if="charge_errors.amount" class="invalid-feedback">
                {{charge_errors.amount[0]}}
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="chargeUser(user.id)">ثبت</button>
        </div>
      </div>
    </modal>

    <modal @close="giveAwardModal = false" :open="giveAwardModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="giveAwardModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          اهدای جعبه شانس
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* تعداد جعبه شانس :</div>
            <div class="col-8">
              <input @keypress="just_float($event)" v-model="get_award_count" class="form-control">

            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="giveAward(user.id)">ثبت</button>
        </div>
      </div>
    </modal>

    <modal @close="giveReferalModal = false" :open="giveReferalModal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="giveReferalModal = false" class="close-modal"><i class="fa fa-times"></i></span>
          اهدای زیر مجموعه
        </div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-4 text-left">* تعداد زیرمجموعه :</div>
            <div class="col-8">
              <input @keypress="just_float($event)" v-model="get_referal_count" class="form-control">

            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="giveReferal(user.id)">ثبت</button>
        </div>
      </div>
    </modal>

  </div>
</template>
<script>
import {TheMask} from 'vue-the-mask'
import {mapActions} from 'vuex'
import Modal from '../../components/Modal.vue'
export default {
  name:'User',
  data(){
    return{
      user:{
        bank_account:{}
      },
      edit_user:{},
      editModal:false,
      modal:false,
      user_modal:false,
      userCharge:false,
      giveAwardModal:false,
      giveReferalModal:false,
      account_id:null,
      reject_reason:null,
      change_status:1,
      get_award_count:null,
      get_referal_count:null,
      errors:{},
      charge:{
        to:1,
        type:1,
      },
      charge_errors:{},
    }
  },
  components:{
    Modal,
    TheMask
  },
  watch:{

  },
  methods:{
    ...mapActions([
      'SPIN_LOADING'
    ]),
    getUser(user_id){
      this.SPIN_LOADING(1)
      this.$axios.get(`users/user/`+user_id)
      .then(res => {
        this.user = res.data.data
        this.edit_user = res.data.data
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    Operate(id , status){
      this.SPIN_LOADING(1)
      this.$axios.post(`account/operate`,{id:id , status:status , reject_reason:this.reject_reason})
      .then(res => {
        this.getUser(this.$route.params.id)
        this.modal = false
        this.reject_reason = null
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.modal = false
        this.reject_reason = null
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    OperateUser(id , status){
      this.SPIN_LOADING(1)
      this.$axios.post(`users/operate`,{id:id , status:status , reject_reason:this.reject_reason})
      .then(res => {
        this.getUser(this.$route.params.id)
        this.user_modal = false
        this.reject_reason = null
        this.change_status = null
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.user_modal = false
        this.reject_reason = null
        this.change_status = null
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    ToggleModal(id){
      this.account_id = id
      this.modal = true
      this.reject_reason = null
    },
    editUser(){
      this.SPIN_LOADING(1)
      this.$axios.post(`users/edit`,this.edit_user)
      .then(res => {
        this.getUser(this.$route.params.id)
        this.editModal = false
        this.modal = false
        this.reject_reason = null
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    chargeUser(user_id){
      this.SPIN_LOADING(1)
      this.$axios.post(`users/charge`,{
        user_id:user_id,
        to:this.charge.to,
        type:this.charge.type,
        amount:this.charge.amount
      })
      .then(res => {
        this.getUser(this.$route.params.id)
        this.userCharge = false
        this.charge.amount = null
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.charge_errors = err.response.data.errors
        this.SPIN_LOADING(0)
      });
    },
    giveAward(user_id){
      if(!this.get_award_count){
        return
      }
      this.SPIN_LOADING(1)
      this.$axios.post(`users/giveAward`,{
        user_id , count : this.get_award_count
      })
      .then(res => {
        this.getUser(this.$route.params.id)
        this.giveAwardModal = false
        this.get_award_count = null
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    },
    giveReferal(user_id){
      if(!this.get_referal_count){
        return
      }
      this.SPIN_LOADING(1)
      this.$axios.post(`users/giveReferal`,{
        user_id , count : this.get_referal_count
      })
      .then(res => {
        this.getUser(this.$route.params.id)
        this.giveReferalModal = false
        this.get_referal_count = null
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
        this.SPIN_LOADING(0)
      });
    },
    checkoutSeller(){
      if(confirm('از تسویه حساب این فروشنده اطمینان کامل دارید؟')){
          this.SPIN_LOADING(1)
          this.$axios.post(`users/checkoutSeller`,{
              user_id : this.user.id
          })
          .then(res => {
              this.getUser(this.$route.params.id)
              this.SPIN_LOADING(0)
          })
          .catch(err => {
              const error = err.response.data
              if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
              this.SPIN_LOADING(0)
          });
      }
    },
  },
  mounted(){
    if(this.$route.params.id !== ''){
      this.getUser(this.$route.params.id)
    }else{
      this.$router.push('/panel/AllUsers');
    }
  }
}
</script>
<style>
</style>
