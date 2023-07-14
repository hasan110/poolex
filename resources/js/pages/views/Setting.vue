<template>
  <div>

    <div class="col-container-8 py-3">
      <div class="container text-light">

        <div class="row mb-3">
            <div class="col-md-4">
                <h4>تنظیمات</h4>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4 text-left">
                <button @click="ads_modal = true" class="btn btn-sm btn-success">تنظیمات تبلیغات</button>
            </div>
        </div>

      <div class="form-row">
        <div class="form-group col-md-4 col-xs-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" v-model="data.allow_record_harvest" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">
              ثبت درخواست برداشت
            </label>
          </div>
        </div>
        <div class="form-group col-md-4 col-xs-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" v-model="data.allow_watch_ads" id="defaultCheck2">
            <label class="form-check-label" for="defaultCheck2">
              تماشای تبلیغ
            </label>
          </div>
        </div>
        <div class="form-group col-md-4 col-xs-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" v-model="data.allow_buy_referral" id="defaultCheck3">
            <label class="form-check-label" for="defaultCheck3">
              خرید رفرال
            </label>
          </div>
        </div>
        <div class="form-group col-md-4 col-xs-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" v-model="data.allow_convert" id="defaultCheck4">
            <label class="form-check-label" for="defaultCheck4">
              انجام تبدیل
            </label>
          </div>
        </div>
        <div class="form-group col-md-4 col-xs-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" v-model="data.allow_get_award" id="defaultCheck5">
            <label class="form-check-label" for="defaultCheck5">
              چرخاندن شانس جایزه
            </label>
          </div>
        </div>
        <div class="form-group col-md-4 col-xs-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" v-model="data.store_offer" id="defaultCheck6">
            <label class="form-check-label" for="defaultCheck6">
              آفر فروشگاه
            </label>
          </div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">سکه لازم برای چرخاندن شانس جایزه</label>
          <input v-model="data.award_coin_reduction" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">اعتبار لازم برای چرخاندن شانس جایزه</label>
          <input v-model="data.award_cash_reduction" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">قیمت هر جایزه برای خرید از درگاه</label>
          <input v-model="data.online_pay_award_cost" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">حداقل میزان برداشت</label>
          <input v-model="data.minimum_harvest_amount" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-12 col-xs-12">
          <label for="inputEmail4">متن درباره ما</label>
          <textarea rows="6" v-model="data.about_us_text" class="form-control form-control-sm" autocomplete="off"></textarea>
        </div>
        <div class="form-group col-md-12 col-xs-12">
          <label for="inputEmail4">متن قوانین فروشگاه</label>
          <textarea rows="6" v-model="data.store_rules_text" class="form-control form-control-sm" autocomplete="off"></textarea>
        </div>
        <div class="form-group col-md-12 col-xs-12">
          <label for="inputEmail4">متن قوانین فروش در پنل فروشندگان</label>
          <textarea rows="6" v-model="data.store_panel_rules_text" class="form-control form-control-sm" autocomplete="off"></textarea>
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">شماره تماس پشتیبانی</label>
          <input v-model="data.support_call_number" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">آیدی اینستاگرام پیج پولکس</label>
          <input v-model="data.instagram_id" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">آیدی تلگرام پولکس</label>
          <input v-model="data.telegram_id" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">شماره واتساپ 1</label>
          <input v-model="data.whatsup_number1" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label for="inputEmail4">شماره واتساپ 2</label>
          <input v-model="data.whatsup_number2" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <div class="form-check py-4">
            <input class="form-check-input" type="checkbox" v-model="data.vpn_required" id="defaultCheck7">
            <label class="form-check-label" for="defaultCheck7">
              vpn اجباری
            </label>
          </div>
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label>آپلود جدیدترین نسخه اپ <span v-if="app_loading">(درحال بارگذاری..)</span></label>
          <input type="file" @change="app_file_selected()" ref="app_file" class="form-control form-control-sm">
        </div>
        <div v-if="data.app_download_link" class="form-group col-md-6 col-xs-12">
          <label>لینک دانلود</label>
          <div class="py-2">
            <a download :href="ImageUrl + data.app_download_link" target="_blank">{{ ImageUrl + data.app_download_link }}</a>
          </div>
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label>شماره ورژن اپ</label>
          <input v-model="data.app_version_code" class="form-control form-control-sm" autocomplete="off">
        </div>
        <div class="form-group col-md-6 col-xs-12">
          <label>هزینه ارسال سفارشات در فروشگاه</label>
          <input v-model="data.stores_shipping_cost" class="form-control form-control-sm">
        </div>
      </div>
      <div class="text-center">
        <button @click="editItem()" class="btn btn-primary">ثبت تغییرات</button>
      </div>
      </div>
    </div>


    <modal @close="ads_modal = false" :open="ads_modal">
      <div class="card m-b-0">
        <div class="card-header bg-primary">
          <span @click="ads_modal = false" class="close-modal"><i class="fa fa-times"></i></span>
          ویرایش تبلیغات ثابت
        </div>
        <div class="card-body">

          <div class="row mb-2">
            <div class="col-4 text-left">بنر تبلیغ 1 :</div>
            <div class="col-8">
              <input type="file" ref="ad_banner1" @change="adBanner1()" class="form-control form-control-sm">
                <div v-if="advertise_errors.ad_banner1" class="invalid-feedback">
                  {{advertise_errors.ad_banner1[0]}}
                </div>
            </div>
          </div>
          <div v-if="data.ad_banner1" class="text-center py-3">
              <img :src="ImageUrl+data.ad_banner1" style="height:150px;width:auto;" alt="ویرایش بنز"/>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">لینک تبلیغ 1  :</div>
            <div class="col-8">
              <input type="text" v-model="data.ad_link1" class="form-control form-control-sm">
                <div v-if="advertise_errors.ad_link1" class="invalid-feedback">
                  {{advertise_errors.ad_link1[0]}}
                </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-4 text-left">بنر تبلیغ 2 :</div>
            <div class="col-8">
              <input type="file" ref="ad_banner2" @change="adBanner2()" class="form-control form-control-sm">
                <div v-if="advertise_errors.ad_banner2" class="invalid-feedback">
                  {{advertise_errors.ad_banner2[0]}}
                </div>
            </div>
          </div>
          <div v-if="data.ad_banner2" class="text-center py-3">
              <img :src="ImageUrl+data.ad_banner2" style="height:150px;width:auto;" alt="ویرایش بنز"/>
          </div>
          <div class="row mb-2">
            <div class="col-4 text-left">لینک تبلیغ 2  :</div>
            <div class="col-8">
              <input type="text" v-model="data.ad_link2" class="form-control form-control-sm">
                <div v-if="advertise_errors.ad_link2" class="invalid-feedback">
                  {{advertise_errors.ad_link2[0]}}
                </div>
            </div>
          </div>

        </div>
        <div class="card-footer">
          <button class="btn btn-primary" @click="SubmitAd">ثبت تغییرات</button>
        </div>
      </div>
    </modal>

  </div>
</template>
<script>
import Modal from '../../components/Modal.vue'
export default {
  name:'Setting',
  components:{
    Modal
  },
  data(){
    return{
      app_loading:false,
      ads_modal:false,
      data:{},
      ad_banners:{},
      errors:{},
      advertise_errors:{},
      app_file:null,
    }
  },
  methods:{
    app_file_selected()
    {
      this.app_file = this.$refs.app_file.files[0];
    },
    adBanner1(){
      this.ad_banners.ad_banner1 = this.$refs.ad_banner1.files[0];
    },
    adBanner2(){
      this.ad_banners.ad_banner2 = this.$refs.ad_banner2.files[0];
    },
    getData(){
      this.SPIN_LOADING(1)
      this.$axios.get(`settings`)
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
    editItem(){
      if(this.app_file)
      {
        this.uploadAppFile()
      }
      this.SPIN_LOADING(1)
      this.$axios.post(`settings/edit` , this.data)
      .then(res => {
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    uploadAppFile(){
      this.app_loading = true
      const form = new FormData();
      form.append('app_file', this.app_file);
      this.$axios.post(`settings/upload_app_file` , form )
      .then( () => {
        this.app_file = null
        this.app_loading = false
      })
      .catch(err => {
        this.app_loading = false
        const error = err.response.data
        if(error.errors){ this.advertise_errors = error.errors }
        else if(error.message){ this.$alert(error.message , "آپلود فایل ناموفق بود." , "error"); }
      });
    },
    SubmitAd(){
      this.SPIN_LOADING(1)

      const form = new FormData();

      const d = this.data

      this.ad_banners.ad_banner1 ? form.append('ad_banner1', this.ad_banners.ad_banner1) : ''
      this.ad_banners.ad_banner2 ? form.append('ad_banner2', this.ad_banners.ad_banner2) : ''
      d.ad_link1 ? form.append('ad_link1', d.ad_link1) : ''
      d.ad_link2 ? form.append('ad_link2', d.ad_link2) : ''

      this.$axios.post(`settings/edit_ads` , form )
      .then( () => {
        this.getData()
        this.advertise_errors = {}
        this.ads_modal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.advertise_errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
  },
  mounted(){
    this.getData()
  }
}
</script>
<style>
</style>
