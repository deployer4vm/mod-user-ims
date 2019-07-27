<template>
  <div class="authentication-wrapper authentication-3">
    <div class="authentication-inner">

      <!-- Side container -->
      <!-- Do not display the container on extra small, small and medium screens -->
      <div class="d-none d-lg-flex col-lg-8 align-items-center ui-bg-cover ui-bg-overlay-container p-5" :style="`background-image: url('${publicUrl}assets/images/login.jpg');`">
        <div class="ui-bg-overlay bg-dark opacity-50"></div>

        <!-- Text -->
        <div class="w-100 text-white px-5">
          <!-- <h1 class="display-2 font-weight-bolder mb-4">SIAPSEDIA</h1>
          <div class="text-large font-weight-light">
            Sistem Informasi Barang Persediaan Kota Bandung<br>
            <small>Badan Pengelolaan Keuangan dan Aset</small>
          </div> -->
        </div>
        <!-- /.Text -->
      </div>

      <!-- Form container -->
      <div class="d-flex col-lg-4 align-items-center bg-white p-5">
        <!-- Inner container -->
        <!-- Have to add `.d-flex` to control width via `.col-*` classes -->
        <div class="d-flex col-sm-7 col-md-5 col-lg-12 px-0 px-xl-4 mx-auto">
          <div class="w-100">

            <!-- Logo -->
            <div class="d-flex justify-content-center align-items-center">
              <div class="ui-w-60">
                <div class="w-100 position-relative" style="padding-bottom: 54%">
                  <img class="w-100 position-absolute" :src="`${publicUrl}assets/images/logo.png`" />
                </div>
              </div>
            </div>
            <!-- / Logo -->

            <h4 class="text-center text-lighter font-weight-normal mt-5 mb-0">{{ title }}</h4>

            <!-- Form -->
            <form class="my-5" @submit="onSubmit" @reset="onReset">
              
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

            </form>
            <!-- / Form -->

            <div class="text-center text-muted" v-if="AppConfig.packageLocal.moduser.registration.enable">
              {{ Trans.get('auth.login.dont_have_an_account') }} 
              <router-link
                tag="a"
                :to="{name: 'register'}"
              >{{ Trans.get('auth.login.signupcaption') }}</router-link>
            </div>

          </div>
        </div>
      </div>
      <!-- / Form container -->
      
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
