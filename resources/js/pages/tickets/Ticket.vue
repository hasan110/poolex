<template>
  <div>
    
    <div v-if="modal" class="dialog">
      <div class="dialog-wrapper">
        <div class="dialog-header"></div>
        <div class="dialog-body p-4">
          
        <div class="form-group">
          <label for="exampleFormControlTextarea1">پاسخ خود را بنویسید :</label>
          <textarea v-model="reply" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>
        </div>
        <div class="dialog-footer">
          <button @click="TicketReply" class="btn btn-sm btn-success">ارسال پاسخ</button>
          <button @click="modal = false" class="btn btn-sm btn-danger">بستن</button>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="content-container">
        <div class="personal-info-box">
          <div class="title text-light">
            <p>جزییات تیکت</p>
          </div>
          <div v-if="data.status == 0 || data.status == 1" class="operate">
            <button @click="modal = true" class="btn btn-success"> پاسخ</button>
            <button @click="OperateItem(data.id , 2)" class="btn btn-danger">بستن تیکت</button>
          </div>
          <div v-if="data.status == 1" class="operate">
            <span class="btn btn-success mx-2"> پاسخ داده شده</span>
          </div>
          <div v-if="data.status == 2" class="operate">
            <span class="btn btn-danger"> بسته شده</span>
          </div>
        </div>
        <br>
        <div class="personal-info-box">
          <div class="row">
            <div class="card" style="width:100%;">
              <div class="card-header">
                <div class="float-right">بخش : {{data.section}}</div>
                <div class="float-left">{{data.date}} - {{data.time}}</div>
              </div>
              <div class="card-body">
                <h5 class="card-title">موضوع : {{data.subject}}</h5>
                <p class="card-text py-2">{{data.text}}</p>
                
              </div>
            </div>
          </div>

          
          <div v-if="data.replies">
            <div v-for="(item , key) in data.replies" :key="key" class="card text-white bg-light mb-3" style="width: 100%;">
              <div class="card-header" :class="[
                item.user_id ? 'bg-primary' : ''
              ]">
                <div class="float-right">
                  <template v-if="item.admin_id">
                  {{item.admin.name}}
                  </template>
                  <template v-if="item.user_id">
                  {{item.user.fullname}}
                  </template>
                </div>
                <div class="float-left">
                  {{item.date}} - {{item.time}}
                </div>
              </div>
              <div class="card-body">
                <p class="card-text p-3">{{item.text}}</p>
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
  name:'Ticket',
  data(){
    return{
      data:{},
      modal:false,
      reject_reason:null,
      reply:null,
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
      this.$axios.get(`ticket/ticket/`+id)
      .then(res => {
        this.SPIN_LOADING(0)
        this.data = res.data.data
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    OperateItem(id , status){
      this.SPIN_LOADING(1)
      this.$axios.post(`ticket/operate`,{id:id , status:status})
      .then(res => {
        this.getData(this.$route.params.id)
        this.modal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.modal = false
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    TicketReply(){
      this.SPIN_LOADING(1)
      this.$axios.post(`ticket/reply`,{id:this.data.id , reply:this.reply})
      .then(res => {
        this.getData(this.$route.params.id)
        this.reply = null
        this.modal = false
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.reply = null
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.errors){ this.errors = error.errors }
        else if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    }
  },
  mounted(){
    if(this.$route.params.id !== ''){
      this.getData(this.$route.params.id)
    }else{
      this.$router.push('/admin/NotResponsedTickets');
    }
  }
}
</script>
<style>
</style>
