<template>
  <div>
    <h3>Create new user</h3>
      <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
  </div>

</template>

<script type="text/babel">
  import VueFormGenerator from "vue-form-generator";
  Vue.use(VueFormGenerator);

  export default {
    data () {
      return {

        model: {
          fname: "Eim Your Stuff Father",
          lname: "Eim Your Stuff Father",
          password: "J0hnD03!x4",
          lvl: "staff",
          fav: 0,
          ppic_a: "1",
          email: "john.doe@gmail.com",
        },

        schema: {
          groups: [
            {
              legend: "User Details",
              fields: [
                {
                  type: "input",
                  inputType: "text",
                  label: "ID (disabled text field)",
                  model: "id",
                  readonly: true,
                  disabled: true
                },
                {
                  type: "input",
                  inputType: "text",
                  label: "First Name",
                  model: "fname",
                  id: "fname",
                  placeholder: "First name",
//                  featured: true,
                  required: true
                },
                {
                  type: "input",
                  inputType: "text",
                  label: "Last Name",
                  model: "lname",
                  id: "lname",
                  placeholder: "Last name",
//                  featured: true,
                  required: true
                },
                {
                  type: "input",
                  inputType: "email",
                  label: "E-mail",
                  model: "email",
                  placeholder: "User's e-mail address"
                },
                {
                  type: "input",
                  inputType: "password",
                  label: "Password",
                  model: "password",
                  min: 6,
                  required: true,
                  hint: "Minimum 6 characters",
                  //validator: validators.string
                }
              ]
            },
            {
              legend: "User role",
              fields: [
                {
                  type: "select",
                  label: "User level",
                  model: "lvl",
                  values: ["owner", "admin", "staff", "client"]
                },
                {
                  type: "checkbox",
                  label: "Favorite",
                  model: "fav",
                  default: 0
                },
                {
                  type: "submit",
                  label: "",
                  caption: "Submit form",
                  validateBeforeSubmit: true,
                  onSubmit(model, schema) {
                    console.log("Form submitted!", model);
                    let headers = {
                      'X-CSRF-Token': jQuery('meta[name=_token]').attr('content'),
                    };

                    axios.post('/user', model, headers).then(response => {
                      console.log(response);
                    });
                  }
                }
              ]
            }
          ],
        },
        formOptions: {
          validateAfterLoad: true,
          validateAfterChanged: true,
          fieldIdPrefix: 'user-'
        }
      }
    },
    methods: {
      addUser: function (e) {
      }
    }
  }
</script>