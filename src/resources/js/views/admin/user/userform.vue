<template>
  <div>
    
    <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
      <router-link class="btn btn-outline-success d-block" :to="{name: 'user.list'}">
        <span class="ion ion-ios-arrow-back"></span>&nbsp; Kembali
      </router-link>
      <div>
        <span class="text-muted font-weight-light">Users /</span>
        {{ title }}
      </div>
    </h4>

    <b-tabs class="nav-tabs-top nav-responsive-sm">
      <b-tab title="Account" active>
        <b-card-body v-if="showUserField('avatar')">

          <div class="media align-items-center">
            <img :src="`${publicUrl}img/avatars/${userData.avatar}`" alt="" class="d-block ui-w-80">
            <div class="media-body ml-3">
              <label class="form-label d-block mb-2">Avatar</label>
              <b-btn variant="outline-primary" size="sm">Change</b-btn>&nbsp;
              <b-btn variant="default md-btn-flat" size="sm">Reset</b-btn>
            </div>
          </div>

        </b-card-body>
        <hr class="border-light m-0" v-if="showUserField('avatar')">
        <b-card-body class="pb-2">

          <b-form-group label="Username" v-if="showUserField('username')">
            <b-input v-model="form.username" class="mb-1" />
            <a href="javascript:void(0)" class="small">Reset password</a>
          </b-form-group>

          <b-form-group label="Email" v-if="showUserField('email')">
            <b-input v-model="form.email" class="mb-1" />
            <a href="javascript:void(0)" class="small" v-if="false">Resend confirmation</a>
          </b-form-group>

          <b-form-group label="Phone" v-if="showUserField('phone')">
            <b-input v-model="form.phone" class="mb-1" />
            <a href="javascript:void(0)" class="small" v-if="false">Resend confirmation</a>
          </b-form-group>

          <b-form-group label="Name">
            <b-input v-model="form.name" />
          </b-form-group>
          
          <b-form-group label="Password" v-if="showUserField('email')">
            <b-input class="mb-1" type="password" value="something" disabled />
            <a href="javascript:void(0)" class="small">Set Password</a>
          </b-form-group>
          
        </b-card-body>
        <hr class="border-light m-0">
        <b-card-body class="pb-2">

          <b-form-group label="Role">
            <b-select v-model="form.role" :options="roles" />
          </b-form-group>

          <b-form-group label="Status">
            <b-select v-model="form.status" :options="{1: 'Active', 2: 'Banned', 3: 'Deleted'}" />
          </b-form-group>

        </b-card-body>
      </b-tab>
      <b-tab title="Password">
        <b-card-body v-if="showUserField('avatar')">

          <div class="media align-items-center">
            <img :src="`${publicUrl}img/avatars/${userData.avatar}`" alt="" class="d-block ui-w-80">
            <div class="media-body ml-3">
              <label class="form-label d-block mb-2">Avatar</label>
              <b-btn variant="outline-primary" size="sm">Change</b-btn>&nbsp;
              <b-btn variant="default md-btn-flat" size="sm">Reset</b-btn>
            </div>
          </div>

        </b-card-body>
        <hr class="border-light m-0" v-if="showUserField('avatar')">
        <b-card-body class="pb-2">

          <b-form-group label="Username" v-if="showUserField('username')">
            <b-input v-model="form.username" class="mb-1" />
            <a href="javascript:void(0)" class="small">Reset password</a>
          </b-form-group>

          <b-form-group label="Email" v-if="showUserField('email')">
            <b-input v-model="form.email" class="mb-1" />
            <a href="javascript:void(0)" class="small" v-if="false">Resend confirmation</a>
          </b-form-group>

          <b-form-group label="Phone" v-if="showUserField('phone')">
            <b-input v-model="form.phone" class="mb-1" />
            <a href="javascript:void(0)" class="small" v-if="false">Resend confirmation</a>
          </b-form-group>

          <b-form-group label="Name">
            <b-input v-model="form.name" />
          </b-form-group>
          
          <b-form-group label="Password">
            <b-input class="mb-1" type="password" />
          </b-form-group>
          
          <b-form-group label="Password">
            <b-input class="mb-1" type="password" />
          </b-form-group>
          
        </b-card-body>
      </b-tab>
      <b-tab title="Profile">
        <b-card-body>
            -
        </b-card-body>
        
      </b-tab>
    </b-tabs>

    <div class="text-right mt-3">
      <b-btn variant="primary">Save changes</b-btn>&nbsp;
      <b-btn variant="default">Cancel</b-btn>
    </div>
  </div>
</template>

<style src="node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>

<!-- Page -->
<style src="@/vendor/styles/pages/users.scss" lang="scss"></style>

<script>
import Multiselect from 'node_modules/vue-multiselect'

export default {
  name: 'pages-user-edit',
  metaInfo: {
    title: 'User edit - Pages'
  },
  components: {
    Multiselect
  },
  data: () => ({
    form: {
      id: 3425433,
      avatar: '',
      name: '',
      username: '',
      email: '',
      verified: true,
      phone: '',
      verified: true,
      role: null,
      status: 1,
      profile: {
        birthday: '',
        country: '',
        languages: '',
        phone: '',
        website: ''
      }
    },
    roles:{1: 'User', 2: 'Admin BPKA', 3: 'Staff', 4: 'Admin'},
    mode: 'create'
  }),
  computed: {    
    title() {
      return this.isAdd ? "Tambah user baru" : "#" + this.form.name;
    }
  },
  created() {
    this.mode = this.$route.name == 'myprofile'?'profile':(this.$route.name == 'user.add'?'create':'udpate');
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
        let data = {};
        if(this.form.username){
          data.username = this.form.username;
        }

        if(this.mode == 'create'){
          this.$store.dispatch("user/register", data).then((res)=>{

          }).catch((err)=>{
            console.log('create user error : ',err);
            this.Web.showAlert({type: 'danger', text: this.Trans.get('alert.auth_failed') });
          });

        }else if(this.mode == 'profile'){
          
          this.$store.dispatch("user/update", {data: data,id: this.form.id}).then((res)=>{

          }).catch((err)=>{
            console.log('update profile error : ',err);
            this.Web.showAlert({type: 'danger', text: this.Trans.get('alert.auth_failed') });
          });
        }else{

          this.$store.dispatch("user/update", {data: data,id: this.form.id}).then((res)=>{

          }).catch((err)=>{
            console.log('update user error : ',err);
            this.Web.showAlert({type: 'danger', text: this.Trans.get('alert.auth_failed') });
          });
        }
        
      }      
    },
    onReset(evt) {
      evt.preventDefault();
      // Reset our form values
      this.form.email = "";
      this.form.password = "";
      this.form.rememberMe = false;
    },
    registerUser(){

    },
    updateUser(){

    },
    updatePassword(password,confirm_passowrd){

    },
    showUserField(field){
      return !this.AppConfig.packageLocal.moduser.users_hidden_field.includes(field);
    },
    showProfileField(field){
      return !this.AppConfig.packageLocal.moduser.user_profiles.hide.includes(field);
    },
    addMusicTag (newTag) {
      this.userData.info.music.push(newTag)
    },
    addMovieTag (newTag) {
      this.userData.info.movies.push(newTag)
    }
  }
}
</script>
