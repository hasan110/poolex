<template>
  <div>
    <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars text-light"></i></a>
        </li>
        <!-- <li class="nav-item d-none d-sm-inline-block">
          <a href="index3.html" class="nav-link">خانه</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">تماس</a>
        </li> -->
      </ul>

      <!-- <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" placeholder="جستجو" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </div>
      </form> -->

      <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">

          <!-- <router-link :to="{name:'Chat'}" class="nav-link">
            <i class="fa fa-comments-o"></i>
            <span v-if="unread_chats" class="badge badge-danger navbar-badge">{{unread_chats}}</span>
          </router-link> -->


          <!-- <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
            <a href="#" class="dropdown-item">
              <div class="media">
                <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 ml-3 img-circle">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    محمدرضا عطوان
                    <span class="float-left text-sm text-danger"><i class="fa fa-star"></i></span>
                  </h3>
                  <p class="text-sm">با من تماس بگیر لطفا...</p>
                  <p class="text-sm text-muted"><i class="fa fa-clock-o mr-1"></i> 4 ساعت قبل</p>
                </div>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <div class="media">
                <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle ml-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    پیمان احمدی
                    <span class="float-left text-sm text-muted"><i class="fa fa-star"></i></span>
                  </h3>
                  <p class="text-sm">من پیامتو دریافت کردم</p>
                  <p class="text-sm text-muted"><i class="fa fa-clock-o mr-1"></i> 4 ساعت قبل</p>
                </div>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <div class="media">
                <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle ml-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    سارا وکیلی
                    <span class="float-left text-sm text-warning"><i class="fa fa-star"></i></span>
                  </h3>
                  <p class="text-sm">پروژه اتون عالی بود مرسی واقعا</p>
                  <p class="text-sm text-muted"><i class="fa fa-clock-o mr-1"></i>4 ساعت قبل</p>
                </div>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">مشاهده همه پیام‌ها</a>
          </div> -->
        </li>
        <li class="nav-item dropdown">

          <!-- <router-link :to="{name:'Notification'}" class="nav-link">
            <i class="fa fa-bell-o"></i>
            <span v-if="notifications_count" class="badge badge-warning navbar-badge">{{notifications_count}}</span>
          </router-link> -->

          <div v-show="not_tab" class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
            <span class="dropdown-item dropdown-header">15 نوتیفیکیشن</span>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fa fa-envelope ml-2"></i> 4 پیام جدید
              <span class="float-left text-muted text-sm">3 دقیقه</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fa fa-users ml-2"></i> 8 درخواست دوستی
              <span class="float-left text-muted text-sm">12 ساعت</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fa fa-file ml-2"></i> 3 گزارش جدید
              <span class="float-left text-muted text-sm">2 روز</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">مشاهده همه نوتیفیکیشن</a>
          </div>
        </li>
      </ul>
    </nav>

    <!-- <div v-if="notification_box" class="notification-box">
      <div class="alert" :class="[notification.color]">{{notification.text}}</div>
    </div> -->

  </div>
</template>
<script>
export default {
  name:'HeaderPanel',
  data(){
    return{
      not_tab:false,
      unread_chats:0,
      notifications_count:0,
      notification:{},
      notification_box:false,
    }
  },
  methods:{
    
    get_unread_chats(){
      this.$axios.get(`chat/unread`)
      .then(res => {
        this.unread_chats = res.data.data
      })
      .catch(err => {
      });
    },
    getNotifications(type){
      this.$axios.get(`notification/list`)
      .then(res => {
        const vm = this
        clearTimeout(timer);
        vm.notifications_count = res.data.data.count
        if(type == 'new'){
          vm.notification = res.data.data.list[0]
          vm.notification_box = true
          var timer = setTimeout(function(){
            vm.notification_box = false
            vm.notification = {}
          }, 5000);
        }
      })
      .catch(err => {
      });
    },

  },
  mounted(){
    // this.get_unread_chats()
    // this.getNotifications('get');

    
  }
}
</script>
<style>
</style>