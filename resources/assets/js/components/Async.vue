<script type="text/babel">
  Vue.component('async-example', function (resolve, reject) {
    console.log('resolving component...')
    setTimeout(function () {
      resolve({
        template: '<div>I am async!</div>'
      })
    }, 1000)
  })
</script>