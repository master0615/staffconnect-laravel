<template>

  <div class="panel panel-default">
    <div class="panel-heading">Current Company: {{ company.uuid }}</div>
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
    data(){
      return {
        task: {
          name: '',
          description: '',
          hostnames: []
        },
        errors: [],
        tasks: []
      }
    },
    mounted()
    {
      this.readTasks();
    },
    methods: {
      readTasks()
      {
        axios.get('/admin').then(response => {
          this.tasks = response.data.tasks;
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