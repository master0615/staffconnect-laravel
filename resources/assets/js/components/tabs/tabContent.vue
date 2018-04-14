<template>
  <component v-bind:is="currentView" v-bind:componentdata="tab.componentData">
    <!-- component changes when vm.currentView changes! -->
  </component>
</template>
<script type="text/babel">

  export default {

    props: {
      tab: {
        type: Object
      },
      currentview: {
        type: null
      }
    },
    data: function () {
      return { currentView: this.currentview }
    },
    methods: {
      loadHtml: function () {

      }
    },
    created: function () {
      this.loadHtml();
    }
  };
</script>