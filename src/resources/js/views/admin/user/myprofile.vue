<template>
    <div>
        <header-breadcrumb :pageTitle="title" :backPath="{name: 'dashboard'}" />     

        <div class="m-3">
            <b-card no-body class="overflow-hidden">
                <div class="row no-gutters row-bordered row-border-light">
                    <div class="col-md-3 pt-0">
                        <b-list-group class="account-settings-links" flush>
                            <b-list-group-item button :active="curTab === 'general'" @click="curTab = 'general'">General</b-list-group-item>
                            <b-list-group-item button :active="curTab === 'password'" @click="curTab = 'password'">Change password</b-list-group-item>

                            <template v-for="(component,i) in profileTabAdds">
                                <b-list-group-item :key="i" button :active="curTab === component.id" @click="curTab = component.id"><!-- v-if="AppConfig.packageLocal.moduser.user_profile_tab.profile.show == 1">-->
                                    <!-- {{ AppConfig.packageLocal.moduser.user_profile_tab.profile.caption }} -->
                                    {{ component.caption }}
                                </b-list-group-item>
                            </template>
                        </b-list-group>
                    </div>

                    <!-- Tab General / Profile -->
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
                                <b-input v-model="userForm.username" :readonly="true" />
                            </b-form-group>

                            <b-form-group label="Name">
                                <b-input v-model="userForm.name" />
                            </b-form-group>

                            <b-form-group label="Email">
                                <b-input v-model="userForm.email" />
                                <b-alert variant="warning" show class="mt-3 mb-0" v-if="false">
                                    Your email is not confirmed. Please check your inbox.
                                    <br />
                                    <a href="javascript:void(0)" v-if="false">Resend confirmation</a>
                                </b-alert>
                            </b-form-group>

                            <div class="text-right mt-3">
                                <b-btn @click="saveUser" variant="primary">Save changes</b-btn>
                            </div>
                        </b-card-body>
                    </div>
                    <!-- / Tab General / Profile -->

                    <!-- Tab Change Password -->
                    <div class="col-md-9" v-if="curTab === 'password'">
                        <b-card-body>
                            <b-form-group label="New password">
                                <b-input type="password" v-model="passwordForm.password" />
                            </b-form-group>
                            <b-form-group label="Repeat new password">
                                <b-input type="password" v-model="passwordForm.password_confirmation" />
                            </b-form-group>

                            <div class="text-right mt-3">
                                <b-btn @click="savePassword" variant="primary">Save changes</b-btn>
                            </div>
                        </b-card-body>
                    </div>
                    <!-- / Tab Change Password -->

                    <template v-for="(component,i) in profileTabAdds">
                        <div class="col-md-9" v-if="curTab === component.id" :key="i">
                            <component :is="component.component"></component>
                        </div>
                    </template>
                </div>
            </b-card>
        </div>
    </div>
</template>

<style src="node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>
<!-- Page -->
<style src="@/vendor/styles/pages/account.scss" lang="scss"></style>

<script>
    import globals from "@/globals";
    import Multiselect from "node_modules/vue-multiselect";
    import { required, minLength, email } from "node_modules/vuelidate/lib/validators";
    
    export default {
        name: "page-myprofile",
        components: {
            Multiselect,
        },
        computed: {},
        data: () => ({
            curTab: "general",
            passwordForm: {
                password: "",
                password_confirmation: "",
            },
            userForm: {
                id: 0,
                avatar: "",
                name: "",
                username: "",
                email: "",
                verified: true,
                phone: "",
                role: null,
                status: 1,
                profile: {},
            },
            profileTabAdds:[]
        }),
        computed: {
            // userForm:{
            //   get() {
            //     return this.$store.state.user.userForm;
            //   },
            //   set(value) {
            //     this.$store.commit('userStore/setUserForm', value);
            //   }
            // }
            title() {
                return this.Trans.get('user.my_profile');
            }
        },
        created() {
            this.userForm = this.UserAuth.getUser();
            this.getUser(this.UserAuth.getUser("id"));
            this.initView();
            var that = this;
            setTimeout(() => {
                that.profileTabAdds.push({
                    caption: 'Profile',
                    component: 'profile-tab-profile',
                    id: 'profile'
                });
            }, 500);
        },
        methods: {
            showUserField(field) {
                return !this.AppConfig.packageLocal.moduser.users_hidden_field.includes(field);
            },
            showProfileField(field) {
                return !this.AppConfig.packageLocal.moduser.user_profiles.hide.includes(field);
            },
            //-----
            getUser(id) {
                this.LocalApi.get(this.AppConfig.endpoint.api.moduser + "/" + id).then((res) => {
                    this.userForm = res.data.data;
                    return res.data.data;
                });
            },
            saveUser() {
                this.LocalApi.put(this.AppConfig.endpoint.api.moduser + "/profile", {
                        id: this.userForm.id,
                        username: this.userForm.username,
                        name: this.userForm.name,
                        email: this.userForm.email,
                    })
                    .then((res) => {
                        this.Web.showAlert({ text: "Profile berhasil disimpan" });
                    })
                    .catch((res) => {
                        let errMessage = "Simpan data gagal : ";
                        if (res.response.data && res.response.data.errors) {
                            _.forEach(res.response.data.errors, (v, i) => {
                                errMessage += "<br> - " + v[0];
                            });
                        } else {
                            errMessage += res.message;
                        }
                        this.Web.showAlert({ text: errMessage, type: "warning" });
                    });
            },
            savePassword() {
                this.LocalApi.put(this.AppConfig.endpoint.api.moduser + "/" + this.userForm.id + "/updatepassword", {
                        id: this.userForm.id,
                        password: this.passwordForm.password,
                        password_confirmation: this.passwordForm.password_confirmation,
                    })
                    .then((res) => {
                        this.Web.showAlert({ text: "Password berhasil diganti" });
                        this.passwordForm.password = "";
                        this.passwordForm.password_confirmation = "";
                    })
                    .catch((res) => {
                        let errMessage = "Simpan password gagal : ";
                        console.log(res.response);
                        if (res.response.data && res.response.data.errors) {
                            _.forEach(res.response.data.errors, (v, i) => {
                                errMessage += "<br> - " + v[0];
                            });
                        } else {
                            errMessage += res.message;
                        }
                        this.Web.showAlert({ text: errMessage, type: "warning" });
                    });
            },
            initView() {
                this.Web.setModule("moduser");

                this.Web.setNavbarTitle(this.title);
                // this.Web.appendNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.PSBBI.access.children.module.caption));

                this.Web.resetBreadcrumb();
                this.Web.addBreadcrumb(this.Trans.get('lang.home'));
                this.Web.addBreadcrumb(this.title);

                this.Web.setBodyWithPadding(false);
                this.Web.setShow("moduser");
            },
        },
    };
</script>
