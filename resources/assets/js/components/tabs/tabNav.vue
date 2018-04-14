<template>
  <a data-toggle='tab' :href="'#'+tab.id">
    {{ tab.title }}
    <span class='glyphicon glyphicon-remove closetab' aria-hidden='true'></span>
  </a>
</template>
<script type="text/babel">
  export default {
    props: {
      tab: {
        type: Object
      }
    },
    mounted: function () {
      var ntab = this.tab;
      if (app.tabCounter < 2 || ntab.active) {
        $("#main-tabs").find("a#a" + ntab.id).click();
      }
    }
  }
</script>