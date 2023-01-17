<template>
  <transition name="fade">
    <div v-if="open" class="modal-wrapper" @click.self="$emit('close')">
      <div class="modal-inner" :style="{maxWidth:maxWitdh}">
        <slot>چیزی درون مودال بگذارید</slot>
      </div>
    </div>
  </transition>
</template>
<script>
export default {
  name:'Modal',
  
  props:{
    open: {
      type: Boolean,
      default: false
    },
    maxWitdh: {
      type: String,
      default: '600px'
    }
  },
  methods:{
  }
}
</script>
<style>
.modal-wrapper{
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1200;
  overflow-y: auto
}
.modal-wrapper .modal-inner{
  /* max-width: 600px; */
  height: 100%;
  background: transparent;
  position: relative;
  top: 50%;
  right: 50%;
  transform: translate(50%,-50%);
}
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
