<template>
  <div>
    <div>mc title is {{ tab.title }}</div>
    <br/>
    <sub-component v-html='html'></sub-component>
    <br/><br/></div>
</template>
<script type="text/babel">

  export default {
    components: {
      'sub-component': {
        template: "<div>{{ html }}</div>",
        props: ['html'],
      }
    },
    data: function () {
      return {
        html: 'loading2'
      }
    },
    props: {
      tab: {
        type: Object
      }
    },
    methods: {
      loadHtml: function () {
        var content = this;
        axios.get('/' + this.tab.href)
          .then(function (response) {
            content.html = response.data;
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    },
    created: function () {
      this.loadHtml();
    }
  };
</script>