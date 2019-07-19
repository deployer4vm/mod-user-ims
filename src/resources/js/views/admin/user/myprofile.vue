<template>
  <div>
    <h4 class="font-weight-bold py-3 mb-4">{{ Trans.get('user.my_profile') }}</h4>

    <b-card no-body class="overflow-hidden">
      <div class="row no-gutters row-bordered row-border-light">
        <div class="col-md-3 pt-0">
          <b-list-group class="account-settings-links" flush>
            <b-list-group-item
              button
              :active="curTab === 'general'"
              @click="curTab = 'general'"
            >General</b-list-group-item>
            <b-list-group-item
              button
              :active="curTab === 'password'"
              @click="curTab = 'password'"
            >Change password</b-list-group-item>

            <b-list-group-item button :active="curTab === 'info'" @click="curTab = 'info'">
              {{AppConfig.packageLocal.moduser.user_profile_tab.profile.caption}}
            </b-list-group-item>
          </b-list-group>
        </div>

        <div class="col-md-9" v-if="curTab === 'general'">

          <b-card-body class="media align-items-center" v-if="showUserField('avatar')">
            <img :src="`${publicUrl}img/avatars/${form.avatar}`" alt class="d-block ui-w-80" />
            <div class="media-body ml-4">
              <b-btn variant="outline-primary">Upload new photo</b-btn>&nbsp;
              <b-btn variant="default md-btn-flat">Reset</b-btn>
              <div class="text-light small mt-1">Allowed JPG, GIF or PNG. Max size of 800K</div>
            </div>
          </b-card-body>

          <hr class="border-light m-0" />
          <b-card-body>
            <b-form-group label="Username">
              <b-input v-model="form.username" />
            </b-form-group>

            <b-form-group label="Name">
              <b-input v-model="form.name" />
            </b-form-group>

            <b-form-group label="Email">
              <b-input v-model="form.email" />
              <b-alert variant="warning" show class="mt-3 mb-0" v-if="!form.email_verified_at">
                Your email is not confirmed. Please check your inbox.
                <br />
                <a href="javascript:void(0)" v-if="false">Resend confirmation</a>
              </b-alert>
            </b-form-group>
            
            <div class="text-right mt-3">
              <b-btn variant="primary">Save changes</b-btn>
            </div>
          </b-card-body>
        </div>

        <div class="col-md-9" v-if="curTab === 'password'">
          <b-card-body>
            <b-form-group label="Current password">
              <b-input type="password" />
            </b-form-group>

            <b-form-group label="New password">
              <b-input type="password" />
            </b-form-group>

            <b-form-group label="Repeat new password">
              <b-input type="password" />
            </b-form-group>
            <div class="text-right mt-3">
              <b-btn variant="primary">Save changes</b-btn>
            </div>
          </b-card-body>
          
        </div>

        <div class="col-md-9" v-if="curTab === 'info'">
          <component :is="'profile-tab-profile'"></component>
        </div>

      </div>

    </b-card>


  </div>
</template>

<style src="node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>
<!-- Page -->
<style src="@/vendor/styles/pages/account.scss" lang="scss"></style>

<script>
import globals from "@/globals";
import Multiselect from "node_modules/vue-multiselect";
import {
  required,
  minLength,
  email
} from "node_modules/vuelidate/lib/validators";
//globals().AppConfig.system.path.MainApp + 

let componentList = {
  Multiselect
}

componentList['profile-tab-profile'] = require("node_modules/../app/MainApp/resources/js/components/moduser/userprofile/profile").default;

export default {
  name: "page-myprofile",
  components: componentList,
  computed: {},
  data: () => ({

    curTab: "general",
    form: {
      id: 3425433,
      avatar: '5-small.png',
      name: 'Nelle Maxwell',
      username: 'nmaxwell',
      email: 'nmaxwell@mail.com',
      verified: true,
      phone: '081321346',
      role: null,
      status: 1,
      profile: {
        birthday: 'May 3, 1995',
        country: 'Canada',
        languages: ['English'],
        phone: '+0 (123) 456 7891',
        website: ''
      }
    }
  }),
  created() {
    this.form = this.UserAuth.getUser();
  },
  methods: {
    showUserField(field){
      return !this.AppConfig.packageLocal.moduser.users_hidden_field.includes(field);
    },
    showProfileField(field){
      return !this.AppConfig.packageLocal.moduser.user_profiles.hide.includes(field);
    }
  }
};
</script>
