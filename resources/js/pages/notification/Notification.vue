<template>
  <div>

    <div class="content-body">
      <div class="content-container p-5">
        <div v-if="!Object.keys(notifications).length" class="text-center text-muted">هیچ اعلانی وجود ندارد</div>
        <div v-for="(notification , key) in notifications" :key="key"  class="alert" :class="[notification.color]">
            <div class="float-left mt-2">
              <template v-if="notification.has_link">
                  <router-link
                    class="btn-notlink btn-sm btn" :class="['btn-'+notification.color]"
                    :to="{name:notification.section , params:{id:notification.model_id}}">
                    <span @click="deleteNotification(notification.id)">بررسی کردن</span>
                  </router-link>
              </template>

              <i @click="deleteNotification(notification.id)" class="fa fa-times px-2"></i>

            </div>
          <div><small v-if="notification.title" class="alert-heading">{{notification.title}}</small></div>
          {{notification.text}}

        </div>
      </div>
    </div>

  </div>
</template>
<script>
import {mapActions} from 'vuex'
export default {
  name:'Notification',
  data(){
    return{
      notifications:{},
      notifications_count:0,
    }
  },
  watch:{
    
  },
  methods:{
    ...mapActions([
      'SPIN_LOADING'
    ]),
    deleteNotification(id){
      this.SPIN_LOADING(1)
      this.$axios.get(`notification/delete/`+id)
      .then(res => {
        this.getNotifications()
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
        const error = err.response.data
        if(error.message){ this.$alert(error.message , "ناموفق" , "error"); }
      });
    },
    getNotifications(){
      this.SPIN_LOADING(1)
      this.$axios.get(`notification/list`)
      .then(res => {
        this.notifications = res.data.data.list
        this.notifications_count = res.data.data.count
        this.SPIN_LOADING(0)
      })
      .catch(err => {
        this.SPIN_LOADING(0)
      });
    }
  },
  mounted(){
    this.getNotifications();
    
    this.socket.on('admin_notification', () => {
      this.getNotifications();
    });
    
    this.socket.on('delete_notification', () => {
      this.getNotifications();
    });

  }
}
</script>
<style>
</style>
