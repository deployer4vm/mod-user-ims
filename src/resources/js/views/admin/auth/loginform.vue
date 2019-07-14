<template>
  <div class="authentication-wrapper authentication-2 ui-bg-cover ui-bg-overlay-container px-4">
    <div class="ui-bg-overlay bg-dark opacity-25"></div>

    <div class="authentication-inner py-5">
      <b-card no-body>
        <div class="p-4 p-sm-5">
          <!-- Logo -->
          <!-- <div class="d-flex justify-content-center align-items-center pb-2 mb-4">
            <div class="ui-w-60">
              <div class="w-100 position-relative" style="padding-bottom: 54%">
                <svg class="w-100 h-100 position-absolute" viewBox="0 0 148 80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient id="a" x1="46.49" x2="62.46" y1="53.39" y2="48.2" gradientUnits="userSpaceOnUse"><stop stop-opacity=".25" offset="0"></stop><stop stop-opacity=".1" offset=".3"></stop><stop stop-opacity="0" offset=".9"></stop></linearGradient><linearGradient id="e" x1="76.9" x2="92.64" y1="26.38" y2="31.49" xlink:href="#a"></linearGradient><linearGradient id="d" x1="107.12" x2="122.74" y1="53.41" y2="48.33" xlink:href="#a"></linearGradient></defs><path class="fill-primary" transform="translate(-.1)" d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z"></path><path transform="translate(-.1)" d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z" fill="url(#a)"></path><path transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z" fill="url(#e)"></path><path transform="translate(-.1)" d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z" fill="url(#d)"></path></svg>
              </div>
            </div>
          </div>-->
          <!-- / Logo -->

          <div class="d-flex justify-content-center align-items-center pb-2">
            <h4>{{ title }}</h4>
          </div>

          <h5
            class="text-center text-muted font-weight-normal mb-4"
          >{{ Trans.get('auth.login.title') }}</h5>
          <alert />
          <!-- Form -->
          <b-form @submit="onSubmit" @reset="onReset">

            <b-form-group :label="Trans.get('auth.login.usernamecaption')" class="position-relative">
              <b-input :state="$v.form.username.$error?'invalid':''" v-model.trim="form.username" @change="$v.form.username.$touch()" />
              <invalid-tooltip :inputItem="$v.form.username" :fieldName="Trans.get('auth.login.usernamecaption')" />
            </b-form-group>

            <b-form-group class="position-relative">
              <div slot="label" class="d-flex justify-content-between align-items-end">
                <div>{{ Trans.get('auth.login.passwordcaption') }}</div>
                <router-link
                  tag="a"
                  :to="{name: 'forgotpassword'}"
                  class="d-block small"
                  v-if="AppConfig.packageLocal.moduser.login.forgotpassword"
                >{{ Trans.get('auth.login.forgotpassword') }}</router-link>
              </div>
              <b-input type="password" :state="$v.form.password.$error?'invalid':''" v-model.trim="form.password" @change="$v.form.password.$touch()" />
              <invalid-tooltip :inputItem="$v.form.password" :fieldName="Trans.get('auth.login.passwordcaption')" />
            </b-form-group>

            <div class="d-flex justify-content-between align-items-center m-0">
              <b-check
                v-model="form.rememberMe"
                class="m-0"
                v-if="AppConfig.packageLocal.moduser.login.rememberme"
              >{{ Trans.get('auth.login.remember_me') }}</b-check>
              <b-btn type="submit" variant="primary">{{ Trans.get('auth.login.sigincaption') }}</b-btn>
            </div>
          </b-form>
          <!-- / Form -->
        </div>

        <b-card-footer
          class="py-3 px-4 px-sm-5"
          v-if="AppConfig.packageLocal.moduser.registration.enable"
        >
          <div class="text-center text-muted">
            {{ Trans.get('auth.login.dont_have_an_account') }}
            <router-link
              tag="a"
              :to="{name: 'register'}"
            >{{ Trans.get('auth.login.signupcaption') }}</router-link>
          </div>
        </b-card-footer>
      </b-card>
    </div>
  </div>
</template>

<!-- Page -->
<style src="@/vendor/styles/pages/authentication.scss" lang="scss"></style>

<script>
import { required, minLength, email } from "node_modules/vuelidate/lib/validators";

export default {
  name: "pages-auth-login",
  metaInfo: {
    title: "Login"
  },
  data: () => ({
    form: {
      username: "",
      password: "",
      rememberMe: false
    }
  }),  
  validations: {
    form: {
      username: {
        required
      },
      password: {
        required,
        minLength: minLength(6)
      }
    }
  },
  computed: {
    title() {      
      return this.Web.getTenantName()?this.Web.getTenantName():this.Web.getAdminTitle();
    }
  },
  methods: {
    onSubmit(evt) {
      evt.preventDefault();
      
      if(this.$v.form.$error){
        this.Web.showAlert({
          type: 'danger', 
          title: this.Trans.get('alert.form_must_complete_title'),
          text: this.Trans.get('alert.form_must_complete_text') 
          });
      }else{
        console.log(this.form.username);
        this.UserAuth.login({
          username: this.form.username,
          password: this.form.password
        }).then((res)=>{          
          console.log('Login success');
          this.Web.showAlert({type: 'success', text: this.Trans.get('alert.auth_success') });
          this.UserAuth.goToDashboard();
        }).catch((err)=>{
          console.log('Login error : ',err);
          this.Web.showAlert({type: 'danger', text: this.Trans.get('alert.auth_failed') });
        });
      }      
    },
    onReset(evt) {
      evt.preventDefault();
      // Reset our form values
      this.form.email = "";
      this.form.password = "";
      this.form.rememberMe = false;
    }
  },
  created() {}
};
</script>
