<template>

  <div class="panel panel-default">
    <div class="panel-heading">Selected Company: {{ company.uuid }}</div>
    <div class="panel-heading">Hostname:
      <div v-for="(hostname, index) in company.hostnames">
        {{ hostname.fqdn }}
      </div>
    </div>
    <div class="panel-body">
      <div class="panel-body">
      </div>
    </div>
  </div>


  </div>
</template>

<script type="text/babel">
  import draggable from 'vuedraggable'

  export default {
    components: {
      draggable,
    },
    props: {
      componentdata: {
        type: Object
      }
    },
    data(){
      return {
        company: {
          uuid: '',
          name: '',
          description: '',
          hostnames: []
        },
        errors: [],
        tasks: [],
      }
    },
    mounted()
    {
      console.log(this.componentdata.companyID);
      this.readCompanyData(this.componentdata.companyID);
    },
    methods: {
      readCompanyData(id)
      {
        axios.get('/admin/company/' + id).then(response => {
          console.log(response.data);
          this.company = response.data.company;
        });
      },
      reset()
      {
        this.task.name = '';
        this.task.description = '';
      },
    }
  }
</script>