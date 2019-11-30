<template>
  <div>
    
    <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
      <router-link class="btn btn-outline-success d-block" :to="{name: 'user.list'}">
        <span class="ion ion-ios-arrow-back"></span>&nbsp; {{Trans.get('lang.back')}}
      </router-link>
      <div>
        <span class="text-muted font-weight-light">{{Trans.get('user.module_caption')}} /</span>
        {{ title }}
      </div>
    </h4>

    <b-tabs class="nav-tabs-top nav-responsive-sm">

      <b-tab :title="Trans.get('user.userform.tab_account_caption')" active>
        <b-card-body v-if="showUserField('avatar')">

          <div class="media align-items-center">
            <img :src="`${publicUrl}img/avatars/${userData.avatar}`" alt="" class="d-block ui-w-80">
            <div class="media-body ml-3">
              <label class="form-label d-block mb-2">{{Trans.get('user.field_caption.avatar')}}</label>
              <b-btn variant="outline-primary" size="sm">{{Trans.get('lang.change')}}</b-btn>&nbsp;
              <b-btn variant="default md-btn-flat" size="sm">{{Trans.get('lang.reset')}}</b-btn>
            </div>
          </div>

        </b-card-body>

        <hr class="border-light m-0" v-if="showUserField('avatar')">

        <b-card-body class="pb-2">

            <b-form-group :label="Trans.get('user.field_caption.name')" class="col position-relative">
                <b-input 
                    :state="$v.form.name.$error?'invalid':''" 
                    @blur="$v.form.name.$touch()"
                    v-model="form.name"
                    placeholder="Name" />
                <invalid-tooltip :inputItem="$v.form.name" :fieldName="'Name'" />
            </b-form-group>

            <b-form-group :label="Trans.get('user.field_caption.username')" class="col position-relative" v-if="showUserField('username')">
                <b-input v-model="form.username" :placeholder="Trans.get('user.field_caption.username')"/>
                <!-- <a href="javascript:void(0)" class="small">Reset password</a> -->
            </b-form-group>

            <b-form-group :label="Trans.get('user.field_caption.email')" class="col position-relative" v-if="showUserField('email')">
                <masked-input
                    :class="{'form-control':true, 'is-invalid':$v.form.email.$error?true:false}" 
                    type="text"
                    :mask="emailMask"
                    :aria-invalid="$v.form.email.$error"
                    v-model.trim="form.email"
                    placeholder="Email"
                />
                <invalid-tooltip :inputItem="$v.form.email" :fieldName="Trans.get('user.field_caption.password')" />
                <a href="javascript:void(0)" class="small" v-if="false">Resend confirmation</a>
            </b-form-group>

            <b-form-group label="Phone" v-if="showUserField('phone')">
                <b-input v-model="form.phone" />
                <a href="javascript:void(0)" class="small" v-if="false">Resend confirmation</a>
            </b-form-group>

        </b-card-body>

        <hr class="border-light m-0">

        <b-card-body class="pb-2">
          
            <b-form-group :label="Trans.get('user.field_caption.password')" class="col position-relative">
                <b-input type="password"
                    :state="$v.form.password.$error?'invalid':''"
                    v-model.trim="form.password"
                    @blur="$v.form.password.$touch()"
                    :placeholder="Trans.get('user.field_caption.password')"
                />
                <invalid-tooltip :inputItem="$v.form.password" :fieldName="Trans.get('user.field_caption.password')" />
            </b-form-group>

            <b-form-group :label="Trans.get('user.field_caption.confirm_password')" class="col position-relative">
                <b-input type="password"
                    :state="$v.form.repassword.$error?'invalid':''"
                    v-model.trim="form.repassword"
                    @blur="$v.form.repassword.$touch()"
                    :placeholder="Trans.get('user.field_caption.confirm_password')"
                />
                <invalid-tooltip :inputItem="$v.form.repassword" :fieldName="Trans.get('user.field_caption.confirm_password')" :customAlert="{sameAsPassword: Trans.get('user.alert.password_not_match')}"/>
            </b-form-group>
          
        </b-card-body>

        <hr class="border-light m-0">

        <b-card-body class="pb-2">

            <b-form-group :label="Trans.get('user.field_caption.role')" class="col position-relative">
                <template v-if="AppConfig.packageLocal.moduser.user_role.multi_role">
                    <b-check-group :state="$v.form.role_code.$error?'invalid':''" v-model="form.role_code" :options="roleItems" class="custom-controls-stacked" />
                </template>
                <template v-else>
                    <b-select 
                        :state="$v.form.role_code.$error?'invalid':''" 
                        v-model="form.role_code" 
                        :options="roleItems" 
                        @blur="$v.form.role_code.$touch()"
                    />
                </template>
                <invalid-tooltip :inputItem="$v.form.role_code" :fieldName="Trans.get('user.field_caption.role')" />
            </b-form-group>

            <b-form-group :label="Trans.get('user.field_caption.status')" class="col position-relative">
                    <b-select v-model="form.status" :options="{1: Trans.get('user.field_caption.status_item.active'), 2: Trans.get('user.field_caption.status_item.banned')}" />                
            </b-form-group>

        </b-card-body>
      </b-tab>


      <b-tab :title="Trans.get('user.userform.tab_profile_caption')" v-if="showProfile">
        <b-card-body>
            -
        </b-card-body>
        
      </b-tab>
    </b-tabs>

    <div class="text-right mt-3">
      <b-btn variant="primary" @click="onSubmit">{{Trans.get('lang.save_change')}}</b-btn>&nbsp;
      <!-- <b-btn variant="default">Cancel</b-btn> -->
    </div>
  </div>
</template>
<!-- Page -->
<style src="@/vendor/styles/pages/users.scss" lang="scss"></style>

<script>
import MaskedInput, { conformToMask } from "node_modules/vue-text-mask";
import { emailMask } from "node_modules/text-mask-addons/dist/textMaskAddons";
import { required, requiredIf, email, sameAs, minLength } from "node_modules/vuelidate/lib/validators";

export default {
    name: 'pages-user-edit',
    metaInfo() {
        return {title: this.Trans.get('user.module_caption')}
    },
    components: {
        MaskedInput
    },
    validations() {
        let data = {
            form: {
                name: {
                    required
                },
                email: {
                    email
                },
                role_code: {
                    required
                },
                password: { 
                    required: requiredIf(function(n) {
                        return this.isAdd;
                    }),
                    minLength: minLength(6)
                },
                repassword: {
                    sameAsPassword: sameAs('password')
                }
            }
        };

        // if(this.form.password != '') {
        //     data.form.password = {        
        //             required: requiredIf(function(n) {
        //                 return this.isAdd;
        //             }),
        //             minLength: minLength(6)
        //         };
        //     data.form.repassword = {
        //             sameAsPassword: sameAs('password')
        //         };
        // }

        return data;
    },
    data: () => ({
        emailMask: emailMask,
        form: {},
        formEmpty: {
            id: 0,
            all_tenant: 0,
            avatar: '',
            name: '',
            username: '',
            email: '',
            phone: '',
            password: '',
            repassword:'',
            role_code: [],
            note: '',
            status: 1,
            profile: {
                gender: 1,
                date_of_birth: null,
                address: '',
                postal_code: ''
            }
        },
        roleItems: {}
    }),
    computed: {  
        isAdd() {
            return this.$route.params.userId ? false : true;
        },  
        title() {
            return this.isAdd ? this.Trans.get('user.userform.form_add_caption') : "#" + this.form.name;
        },
        oneData: {
            get() {
                return this.$store.state.user.user;
            },
            set(value) {
                this.$store.commit("user/setUser", value);
            }
        },
        //access config
        showProfile () {
            return this.AppConfig.packageLocal.moduser.user_profiles.hide_all==0;
        }
    },
    created() {
        //load data role
        this.$store.dispatch("role/roleList").then((res)=>{
            let tmpRoleItems = [];
            _.forEach(res.data,(v,i)=>{
                tmpRoleItems.push({value: v.role_code, text: '[' + v.role_code + '] ' + v.name});
            })
            this.roleItems = tmpRoleItems;
        });

        this.loadData();
    },
    methods: {
        loadData() {
            if(!this.isAdd){        
                this.$store.dispatch(
                    'user/getUser',this.$route.params.userId                    
                ).then((res)=>{
                    this.form = this.oneData;
                    this.form.role_code = [];
                    _.forEach(this.form.user_role,(v,i)=>{
                        this.form.role_code.push(v.role_code);
                    })
                    this.form.password = '';
                    this.form.repassword = '';
                }).catch((res)=>{
                    console.log('get User error : ',res);
                    this.Web.showAlert({text: "Get Data Error",style: "warning"});
                });
            }else{      
                this.form = this.formEmpty;
            }
        },
        onSubmit(evt) {
            evt.preventDefault();   
            this.$v.$touch();  
            if (this.$v.$invalid) {
                this.Web.showAlert({
                    type: 'danger', 
                    title: this.Trans.get('alert.form_must_complete_title'),
                    text: this.Trans.get('alert.form_must_complete_text') 
                });
            }else{

                if(this.isAdd){
                    this.$store.dispatch("user/register", this.form).then((res)=>{
                        this.Web.showAlert({type: 'info', text: 'User Registered Successfully' });
                        this.$router.push({name: 'user.list'});
                    }).catch((err)=>{
                        console.log('add User error : ',err);
                        this.Web.showAlert({type: 'danger', text: 'Simpan data gagal : ' + err.message });
                    });
                }else{

                    this.$store.dispatch("user/update", {data: this.form,id: this.form.id}).then((res)=>{
                        this.Web.showAlert({type: 'info', text: 'User Updated Successfully' });
                        this.$router.push({name: 'user.list'});
                    }).catch((err)=>{
                        console.log('update user error : ',err);
                        this.Web.showAlert({type: 'danger', text: 'Simpan data gagal : ' + err.message });
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
        showUserField(field){
            return !this.AppConfig.packageLocal.moduser.users_hidden_field.includes(field);
        },
        showProfileField(field){
            return !this.AppConfig.packageLocal.moduser.user_profiles.hide.includes(field);
        }
    }
}
</script>
