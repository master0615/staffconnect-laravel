<template>
  <div class="panel panel-default">
    <div class="panel-heading">All active companies</div>
    <div class="panel-body">
      <div class="panel-body">
        <table class="table table-bordered table-striped table-responsive" v-if="tasks.length > 0">
          <tbody>
          <tr>
            <th>
              ID.
            </th>
            <th>
              Name
            </th>
            <th>
              Hostname
            </th>
          </tr>
          <draggable v-model="tasks" :options="{draggable:'.item'}">
            <tr v-for="(company, index) in tasks" class="item" :key="task.id" v-on:newTabChild="newTab">
              <td>{{ company.id }}</td>
              <td>
                {{ company.uuid }}
              </td>
              <td>
                <div v-for="(hostname, index) in company.hostnames">
                  <div @click="newTab('title2','admin/company/' +  task.id)">{{ hostname.fqdn }}</div>
                </div>
              </td>
            </tr>
          </draggable>

          </tbody>
        </table>
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
      newTab(title, url) {
        this.$emit('newTabChild', title, url);
      },
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