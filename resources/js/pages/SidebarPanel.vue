<template>
  <div>
    <aside class="main-sidebar sidebar-dark-warning elevation-4">

      <div class="sidebar" style="direction: rtl">
        <div style="direction: rtl">
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
              <img :src="StaticUrl+'assets/img/empty-user.png'" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
              <a class="d-block">{{admin.name}}</a>
            </div>
          </div>

          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


              <li class="nav-item">
                <router-link :to="{name:'Dashboard'}" class="nav-link">
                  <p>
                    <i class="fa fa-dashboard"></i>
                    گزارش
                  </p>
                </router-link>
              </li>

              <li class="nav-item">
                <router-link :to="{name:'Users'}" class="nav-link">
                  <p>
                    <i class="fa fa-users"></i>
                    کاربران
                  </p>
                </router-link>
              </li>

              <li class="nav-item">
                <router-link :to="{name:'UserAwards'}" class="nav-link">
                  <p>
                    <i class="fa fa-gift"></i>
                    جوایز کاربران
                  </p>
                </router-link>
              </li>

              <li class="nav-item">
                <router-link :to="{name:'Harvests'}" class="nav-link">
                  <p>
                    <i class="fa fa-history"></i>
                    برداشت ها
                  </p>
                </router-link>
              </li>

              <!-- <li class="nav-item">
                <router-link :to="{name:'Users'}" class="nav-link">
                  <p>
                    <i class="fa fa-users"></i>
                    کاربران
                  </p>
                </router-link>
              </li> -->

              <li class="nav-item">
                <router-link :to="{name:'Advertise'}" class="nav-link">
                  <p>
                    <i class="fa fa-id-card-o"></i>
                    تبلیغات
                  </p>
                </router-link>
              </li>

              <li class="nav-item">
                <router-link :to="{name:'Video'}" class="nav-link">
                  <p>
                    <i class="fa fa-film"></i>
                    فیلم و سریال
                  </p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{name:'Category'}" class="nav-link">
                  <p>
                    <i class="fa fa-sliders"></i>
                    دسته بندی/ژانر فیلم
                  </p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{name:'VideoApi'}" class="nav-link">
                  <p>
                    <i class="fa fa-sliders"></i>
                    فیلم های api
                  </p>
                </router-link>
              </li>

              <li class="nav-item has-treeview" :class="[
                this.$route.name == 'Ticket' ||
                this.$route.name == 'NotResponsedTickets' ||
                this.$route.name == 'ResponsedTickets' ||
                this.$route.name == 'ClosedTickets' ?
                'menu-open' : '']"
              >
                <a href="#" class="nav-link" :class="[this.$route.name == 'Ticket' || this.$route.name == 'NotResponsedTickets' || this.$route.name == 'ResponsedTickets' || this.$route.name == 'ClosedTickets' ? 'active' : '']">
                  <i class="fa fa-ticket"></i>
                  <p>
                    پشتیبانی (تیکت ها)
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <router-link :to="{name:'NotResponsedTickets'}" class="nav-link">
                      <i class="fa fa-circle-o text-warning"></i>
                      <p>تیکت های پاسخ داده نشده</p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'ResponsedTickets'}" class="nav-link">
                      <i class="fa fa-circle-o text-success"></i>
                      <p>تیکت های پاسخ داده شده</p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'ClosedTickets'}" class="nav-link">
                      <i class="fa fa-circle-o text-danger"></i>
                      <p>تیکت های بسته شده</p>
                    </router-link>
                  </li>
                </ul>
              </li>
              <li class="nav-item has-treeview" :class="[
                this.$route.name == 'Store' ||
                this.$route.name == 'Award' ||
                this.$route.name == 'Help' ||
                this.$route.name == 'Slider' ||
                this.$route.name == 'Plan' ||
                this.$route.name == 'Rule' ||
                this.$route.name == 'SlideShow' ||
                this.$route.name == 'Message' ||
                this.$route.name == 'Setting' ?
                'menu-open' : '']"
              >
                <a href="#" class="nav-link" :class="[
                  this.$route.name == 'Store' ||
                  this.$route.name == 'Award' ||
                  this.$route.name == 'Help' ||
                  this.$route.name == 'Slider' ||
                  this.$route.name == 'Plan' ||
                  this.$route.name == 'Rule' ||
                  this.$route.name == 'SlideShow' ||
                  this.$route.name == 'Message' ||
                  this.$route.name == 'Setting' ? 'active' : '']">
                  <i class="fa fa-line-chart"></i>
                  <p>
                    مدیریت سایت
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <router-link :to="{name:'Store'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        محصولات فروشگاه
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'Award'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        جوایز قرعه کشی
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'Help'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        به کمک نیاز دارید
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'Slider'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        اسلاید های راهنمایی
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'Rule'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        قوانین
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'Message'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        پیام ها
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'SlideShow'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        اسلایدشو ها
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'Plan'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        پلن ها
                      </p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link :to="{name:'Setting'}" class="nav-link">
                      <p>
                        <i class="fa fa-circle-o"></i>
                        تنظیمات
                      </p>
                    </router-link>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <router-link :to="{name:'Notification'}" class="nav-link">
                  <p>
                    <i class="fa fa-bell"></i>
                    نوتیفیکیشن ها
                  </p>
                </router-link>
              </li>


              <!-- <li class="nav-item">
                <router-link :to="{name:'Admins'}" class="nav-link">
                  <p>
                    <img :src="StaticUrl+'assets/img/icons/admin.svg'">
                    ادمین ها
                  </p>
                </router-link>
              </li> -->


              <!-- <li class="nav-item">
                <a href="pages/widgets.html" class="nav-link">
                  <i class="nav-icon fa fa-th"></i>
                  <p>
                    ویجت‌ها
                    <span class="right badge badge-danger">جدید</span>
                  </p>
                </a>
              </li>
              <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link">
                  <i class="nav-icon fa fa-pie-chart"></i>
                  <p>
                    چارت‌ها
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="pages/charts/chartjs.html" class="nav-link">
                      <i class="fa fa-circle-o nav-icon"></i>
                      <p>نمودار ChartJS</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/charts/flot.html" class="nav-link">
                      <i class="fa fa-circle-o nav-icon"></i>
                      <p>نمودار Flot</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/charts/inline.html" class="nav-link">
                      <i class="fa fa-circle-o nav-icon"></i>
                      <p>نمودار Inline</p>
                    </a>
                  </li>
                </ul>
              </li>  -->
            </ul>
          </nav>
        </div>
      </div>
    </aside>
  </div>
</template>
<script>
export default {
  name:'SidebarPanel',
  data(){
    return{
    }
  },
  props:{
    admin: {
      type: Object,
      required: true
    }
  },
  methods:{

  },
  mounted(){
  }
}
</script>
<style>
</style>
