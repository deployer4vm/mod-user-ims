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
            <br><br>
            <h5 class="text-center text-muted font-weight-normal mb-4">{{ Trans.get('auth.forgotpassword.title') }}</h5>

            <p>
              {{ Trans.get('auth.forgotpassword.description') }}
            </p>

            <!-- Form -->
            <form class="my-5">
              <b-form-group>
                  <b-input v-model="credentials.email" :placeholder="Trans.get('auth.forgotpassword.emailcaption')" />
              </b-form-group>

              <b-btn variant="primary" :block="true">{{ Trans.get('auth.forgotpassword.button') }}</b-btn>
            </form>

            <div class="w-100 py-3 px-4 px-sm-5" >
              <div class="text-center text-muted">
                <router-link tag="a" :to="{name: 'login'}" class="d-block small">{{ Trans.get('auth.forgotpassword.back_to_login') }}</router-link>
              </div>
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
export default {
  name: 'pages-authentication-password-reset',
  metaInfo: {
    title: 'Password reset - Pages'
  },
  data: () => ({
    credentials: {
      email: ''
    }
  }),
  computed: {
    title() {
      return this.Web.getAdminTitle();
    }
  },
  created() {
      
  }
}
</script>
