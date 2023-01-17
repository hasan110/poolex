<template>
  <div>
    <keep-alive :include="['Notification']">
      <router-view></router-view>
    </keep-alive>

    <div v-if="SPIN_LOADING_STATE" class="lds-wrapper">
      <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>

  </div>
</template>
<script>
import { mapState } from 'vuex' 
export default {
  name:'LoaderPanel',
  computed:{
    ...mapState([
      'SPIN_LOADING_STATE'
    ])
  }
}
</script>
<style>
.swal2-container{
  z-index: 99999 !important;
}
</style>
