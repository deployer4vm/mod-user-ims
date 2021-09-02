<template>
    <div class="authentication-wrapper authentication-3">
        <div class="authentication-inner">
            <!-- Side container -->
            <!-- Do not display the container on extra small, small and medium screens -->
            <div class="d-none d-lg-flex col-lg-8 align-items-center ui-bg-cover ui-bg-overlay-container p-5" :style="`background-image: url('${publicUrl}assets/images/login.jpg');`">
                <!-- <div class="ui-bg-overlay bg-dark opacity-50"></div> -->

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
                            <div style="width: 200px;">
                                <div class="w-100 position-relative" style="padding-bottom: 54%">
                                    <img class="w-100 position-absolute" :src="`${publicUrl}assets/images/logo.png`" />
                                </div>
                            </div>
                        </div>
                        <!-- / Logo -->

                        <h4 class="text-center font-weight-normal mt-5 mb-0">{{ title }}</h4>
                        
                        <alert />
                        
                        <!-- Form -->
                        <form class="my-5" @submit="onSubmit" @reset="onReset">
                            <b-form-group :label="Trans.get('auth.register.namecaption')" class="position-relative">
                                <b-input-group>
                                    <b-input-group-text slot="prepend"><i class="ion ion-md-contact"></i></b-input-group-text>
                                    <b-input :state="$v.form.name.$error ? false : ''" v-model.trim="form.name" @change="$v.form.name.$touch()" />
                                </b-input-group>
                                <invalid-tooltip :inputItem="$v.form.name" :fieldName="Trans.get('auth.register.namecaption')" />
                            </b-form-group>

                            <b-form-group :label="Trans.get('auth.register.emailcaption')" class="position-relative">
                                <b-input-group>
                                    <b-input-group-text slot="prepend"><i class="ion ion-md-contact"></i></b-input-group-text>
                                    <b-input :state="$v.form.email.$error ? false : ''" v-model.trim="form.email" @change="$v.form.email.$touch()" />
                                </b-input-group>
                                <invalid-tooltip :inputItem="$v.form.email" :fieldName="Trans.get('auth.register.emailcaption')" />
                            </b-form-group>

                            <b-form-group class="position-relative">
                                <div slot="label" class="d-flex justify-content-between align-items-end">
                                    <div>{{ Trans.get("auth.register.passwordcaption") }}</div>
                                </div>
                                <b-input-group>
                                    <b-input-group-text slot="prepend"><i class="ion ion-md-lock"></i></b-input-group-text>
                                    <b-input type="password" :state="$v.form.password.$error ? false : ''" v-model.trim="form.password" @change="$v.form.password.$touch()" />
                                </b-input-group>
                                <invalid-tooltip :inputItem="$v.form.password" :fieldName="Trans.get('auth.register.passwordcaption')" />
                            </b-form-group>

                            <b-form-group class="position-relative">
                                <div slot="label" class="d-flex justify-content-between align-items-end">
                                    <div>{{ Trans.get("auth.register.repasswordcaption") }}</div>
                                </div>
                                <b-input-group>
                                    <b-input-group-text slot="prepend"><i class="ion ion-md-lock"></i></b-input-group-text>
                                    <b-input type="password" :state="$v.form.repassword.$error ? false : ''" v-model.trim="form.repassword" @change="$v.form.repassword.$touch()" />
                                </b-input-group>
                                <invalid-tooltip :inputItem="$v.form.repassword" :fieldName="Trans.get('auth.register.repasswordcaption')" />
                            </b-form-group>

                            <b-btn type="submit" class="btn-block" variant="primary">{{ Trans.get("auth.register.signupcaption") }}</b-btn>  
                        </form>
                        <!-- / Form -->

                        <div class="text-center text-muted" v-if="AppConfig.packageLocal.moduser.registration.enable">
                            {{ Trans.get("auth.register.already_have_an_account") }}
                            <router-link tag="a" :to="{ name: 'login' }">{{ Trans.get("auth.register.sigincaption") }}</router-link>
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
    import { required, minLength, sameAs, email } from "node_modules/vuelidate/lib/validators";

    export default {
        name: "pages-auth-registrasi",
        metaInfo: {
            title: "Registrasi",
        },
        data: () => ({
            form: {
                role_code:"user",
                name: "",
                email: "",
                phone: "",
                password: "",
                repassword: "",
                tos_confirm: 0,
            },
            formEmpty: {
                role_code:"user",
                name: "",
                email: "",
                phone: "",
                password: "",
                repassword: "",
                tos_confirm: 0,
            },
        }),
        validations: {
            form: {
                name: {
                    required
                },
                email: {
                    required,
                    email
                },
                phone: {
                    required: requiredIf(function (nestedModel) {
                        return this.showUserField('phone');
                    })
                },  
                password: {
                    required,
                    minLength: minLength(6)
                },
                repassword: {
                    sameAsPassword: sameAs("password")
                }
            },
        },
        computed: {
            title() {
                return this.Web.getTenantName()
                    ? this.Web.getTenantName()
                    : this.Web.getAdminTitle();
            },
        },
        methods: {
            onSubmit(evt) {
                evt.preventDefault();

                this.$v.$touch();
                if (this.$v.form.$error) {
                    this.Web.showAlert({
                        type: "danger",
                        title: this.Trans.get("alert.form_must_complete_title"),
                        text: this.Trans.get("auth.register.alert.register_failed",{error:this.Trans.get("alert.form_must_complete_text")})
                    });
                    return false;
                }

                if(this.form.password != this.form.repassword){
                    this.Web.showAlert({
                        type: "danger",
                        title: this.Trans.get("alert.form_must_complete_title"),
                        text: this.Trans.get("auth.register.alert.register_failed",{error:this.Trans.get("auth.register.alert.password_not_match")})
                    });
                    return false;
                }

                this.UserAuth.register(this.form)
                    .then(res => {
                        this.Web.showAlert({
                            type: "success",
                            text: res.message?res.message:this.Trans.get("register.alert.register_success")
                        });
                        this.UserAuth.goToHome();
                    })
                    .catch(err => {
                        console.log("Login error : ", err);
                        this.Web.showAlert({
                            type: "danger",
                            text: this.Trans.get("auth.register.alert.register_failed",{error:err.message})
                        });
                    });
            },
            onReset(evt) {
                evt.preventDefault();
                // Reset our form values
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
            },            
            showUserField(field) {
                return !this.AppConfig.packageLocal.moduser.users_hidden_field.includes(field);
            },
        },
        created() {
            if(this.$route.query.role_code){
                this.formEmpty.role_code = this.$route.query.role_code;
                this.form.role_code = this.$route.query.role_code;
            }
            if(this.$route.query.email){
                this.formEmpty.email = this.$route.query.email;
                this.form.email = this.$route.query.email;
            }
            if(this.$route.query.name){
                this.formEmpty.name = this.$route.query.name;
                this.form.name = this.$route.query.name;
            }
            if(this.$route.query.phone){
                this.formEmpty.phone = this.$route.query.phone;
                this.form.phone = this.$route.query.phone;
            }
                        
        },
    };
</script>