<template>
    <div>
        <header-breadcrumb :pageTitle="pageTitle" :backPath="{name: 'manageapi.list'}" />

        <div class="m-3" v-if="isDataLoaded">
            <b-tabs class="nav-tabs-top nav-responsive-sm">
                <b-tab :title="Trans.get('user.userform.tab_account_caption')" active>
                    <b-card-body class="pb-2">
                        <b-form-group :label="Trans.get('user.field_caption.name')" class="col position-relative">
                            <b-input :state="$v.form.name.$error ? 'invalid' : ''" @blur="$v.form.name.$touch()" v-model="form.name" placeholder="Name" />
                            <invalid-tooltip :inputItem="$v.form.name" :fieldName="'Name'" />
                        </b-form-group>
                        
                        <b-form-group :label="Trans.get('user.field_caption.role')" class="col position-relative">
                            <template v-if="AppConfig.packageLocal.moduser.user_role.multi_role">
                                <b-check-group :state="$v.form.role_code.$error ? 'invalid' : ''" v-model="form.role_code" :options="roleItems" class="custom-controls-stacked" />
                            </template>
                            <template v-else>
                                <b-select :state="$v.form.role_code.$error ? 'invalid' : ''" v-model="form.role_code" :options="roleItems" @blur="$v.form.role_code.$touch()" />
                            </template>
                            <invalid-tooltip :inputItem="$v.form.role_code" :fieldName="Trans.get('user.field_caption.role')" />
                        </b-form-group>
                    </b-card-body>


                    <hr class="border-light m-0" v-if="!isAdd"/>
                
                    <b-card-body class="pb-2" v-if="!isAdd">
                        <b-form-group :label="Trans.get('user.field_caption.generate_token')" class="col position-relative">
                            <div class="input-group mb-3">
                              <input type="text" class="form-control" v-model="form.api_token.api_token" placeholder="Generate Token" readonly>
                              <div class="input-group-append">
                                <button class="btn btn-outline-warning" type="button" @click="generateTokenSystem(form.id)">Generate Token</button>
                              </div>
                              <div class="input-group-append">
                                <button class="btn btn-outline-info" type="button" v-if="form.api_token.api_token"
                                    v-clipboard:copy="form.api_token.api_token" v-clipboard:success="copySuccess"
                                    v-clipboard:error="copyFail">
                                    Copy Token
                                </button>
                              </div>
                            </div>
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
                <b-btn variant="primary" @click="onSubmit">{{ Trans.get("lang.save_change") }}</b-btn
                >&nbsp;
                <!-- <b-btn variant="default">Cancel</b-btn> -->
            </div>
        </div>
        <div v-else>
            <div class="text-mutted h-row align-items-center" style="width: 100%;">
                <div class="col">
                    <div class="sk-cube-grid sk-primary">
                        <div class="sk-cube sk-cube1"></div>
                        <div class="sk-cube sk-cube2"></div>
                        <div class="sk-cube sk-cube3"></div>
                        <div class="sk-cube sk-cube4"></div>
                        <div class="sk-cube sk-cube5"></div>
                        <div class="sk-cube sk-cube6"></div>
                        <div class="sk-cube sk-cube7"></div>
                        <div class="sk-cube sk-cube8"></div>
                        <div class="sk-cube sk-cube9"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<!-- Page -->
<style src="@/vendor/styles/pages/users.scss" lang="scss"></style>

<script>
    import MaskedInput, { conformToMask } from "node_modules/vue-text-mask";
    import { required } from "node_modules/vuelidate/lib/validators";
    import VueClipboard from 'vue-clipboard2'
    Vue.use(VueClipboard)

    export default {
        name: "moduser-user-form",
        metaInfo() {
            return { title: this.pageTitle };
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
                    role_code: {
                        required
                    },
                }
            };

            return data;
        },
        data: () => ({
            accessRuleKey: "moduser.manage_api",
            form: {},
            formEmpty: {
                id: 0,
                all_tenant: 0,
                name: "",
                role_code: [],
                note: "",
                status: 1,
                profile: {
                    gender: 1,
                    date_of_birth: null,
                    address: "",
                    postal_code: ""
                }
            },
            roleItems: {},
            isDataLoaded: true,
            // upload
            showUpload: false,
            otherParams: {
                token: '123456798',
                name: 'img'
            },
            generateToken: "",
        }),
        computed: {
            isAdd() {
                return this.$route.params.userId ? false : true;
            },

            pageTitle() {
                return this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.manage_api.caption);
            },
            title() {
                return this.isAdd ? this.Trans.get("user.userform.form_add_caption") : ("#" + this.form.name);
            },
            oneData: {
                get() {
                    return this.$store.state.usersystem.user;
                },
                set(value) {
                    this.$store.commit("usersystem/setUser", value);
                }
            },
            //access config
            showProfile() {
                return this.AppConfig.packageLocal.moduser.user_profiles.hide_all == 0;
            }
        },
        created() {
            if (!(this.UserAuth.hasAccess(this.accessRuleKey, "c") || this.UserAuth.hasAccess(this.accessRuleKey, "u"))) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({
                    title: this.Trans.get("alert.warning_title"),
                    text: this.Trans.get("alert.access_denied"),
                    type: "warning"
                });
                return false;
            }

            // this.Web.setNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.caption));
            // this.Web.appendNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.user.caption));

            if (this.AppConfig.packageLocal.moduser.user_role.multi_role == 0) {
                this.form.role_code = "";
            }

            //load data rolesystem
            this.$store.dispatch("rolesystem/roleList", { system_role: 1 }).then(res => {
                let tmpRoleItems = [];
                _.forEach(res.data, (v, i) => {
                    tmpRoleItems.push({ value: v.role_code, text: "[" + v.role_code + "] " + v.name });
                });
                this.roleItems = tmpRoleItems;
            });

            this.loadData();
            this.Web.setBodyWithPadding(false);
        },
        methods: {
            loadData() {
                if (!this.isAdd) {
                    this.isDataLoaded = false;
                    this.$store
                        .dispatch("usersystem/getUser", this.$route.params.userId)
                        .then(res => {
                            this.checkSystemUser(res)
                            this.form = this.oneData;
                            if (this.AppConfig.packageLocal.moduser.user_role.multi_role) this.form.role_code = [];
                            _.forEach(this.form.user_role, (v, i) => {
                                if (this.AppConfig.packageLocal.moduser.user_role.multi_role) {
                                    this.form.role_code.push(v.role_code);
                                } else {
                                    this.form.role_code = v.role_code;
                                    return true;
                                }
                            });
                            this.isDataLoaded = true;
                            
                            if (!this.form.api_token) {
                                this.form.api_token = {
                                    api_token: "",
                                }
                            }

                            this.initView();
                        })
                        .catch(res => {
                            this.isDataLoaded = true;
                            console.log("get User error : ", res);
                            this.Web.showAlert({ text: "Get Data Error", style: "warning" });
                        });
                } else {
                    this.isDataLoaded = true;
                    this.form = this.formEmpty;

                    this.initView();
                }
            },
            onSubmit(evt) {
                evt.preventDefault();
                this.$v.$touch();
                if (this.$v.$invalid) {
                    this.Web.showAlert({
                        type: "danger",
                        title: this.Trans.get("alert.form_must_complete_title"),
                        text: this.Trans.get("alert.form_must_complete_text")
                    });
                } else {
                    if (this.isAdd) {
                        if (!this.UserAuth.hasAccess(this.accessRuleKey, "c")) {
                            //goto dashboard current tenant
                            this.Web.goToCurrentTenant();
                            this.Web.showAlert({ text: this.Trans.get("alert.access_denied"), type: "warning" });
                            return false;
                        }

                        this.$store
                            .dispatch("usersystem/register", this.form)
                            .then(res => {
                                this.Web.showAlert({ type: "info", text: "User Registered Successfully" });
                                this.$router.push({ name: "manageapi.list" });
                            })
                            .catch(err => {
                                console.log("add User error : ", err);
                                this.Web.showAlert({ type: "danger", text: "Simpan data gagal : " + err.message });
                            });
                    } else {
                        if (!this.UserAuth.hasAccess(this.accessRuleKey, "u")) {
                            //goto dashboard current tenant
                            this.Web.goToCurrentTenant();
                            this.Web.showAlert({ text: this.Trans.get("alert.access_denied"), type: "warning" });
                            return false;
                        }

                        this.$store
                            .dispatch("usersystem/update", { data: this.form, id: this.form.id })
                            .then(res => {
                                this.Web.showAlert({ type: "info", text: "User System Updated Successfully" });
                                this.$router.push({ name: "manageapi.list" });
                            })
                            .catch(err => {
                                console.log("update user system error : ", err);
                                this.Web.showAlert({ type: "danger", text: "Simpan data gagal : " + err.message });
                            });
                    }
                }
            },
            onReset(evt) {
                evt.preventDefault();
                // Reset our form values
                this.form.name = "";
            },
            showUserField(field) {
                return !this.AppConfig.packageLocal.moduser.users_hidden_field.includes(field);
            },
            showProfileField(field) {
                return !this.AppConfig.packageLocal.moduser.user_profiles.hide.includes(field);
            },
            uploadAvatar() {
                this.showUpload = this.showUpload?false:true;
            },
            resetdAvatar() {

            },
            copySuccess() {
                this.Web.showAlert({ text: "Copy success" });
                this.loadData();
            },
            copyFail() {
                this.Web.showAlert({ text: "Copy fail" });
            },
            generateTokenSystem(id){
                this.Web.showAlert({
                    styleType: "modal",
                    style: "warning",
                    title: "Generate Confirmation",
                    text: "Are you sure ?",
                    modalButtonCancel: "No",
                    modalButtonOk: "Yes",
                    onOk: () => {
                        this.$store.dispatch("usersystem/generateToken", id)
                            .then(res => {
                                this.Web.showAlert({ text: "Token generated" });
                                this.loadData();
                            })
                            .catch(res => {
                                this.Web.showAlert({ text: "Token generated fail", style: "warning" });
                            });
                        }
                });
            },
            checkSystemUser(user){
                if(user.system_user !== 1){
                    this.$router.push({ name: "user.list" });
                    this.Web.showAlert({ text: this.Trans.get("alert.access_denied"), type: "warning" });
                }
            },
            //--------------------------
            initView() {
                this.Web.setModule("moduser");

                this.Web.setNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.manage_api.caption));
                // this.Web.appendNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.PSBBI.access.children.module.children.manage_api.caption));

                this.Web.resetBreadcrumb();
                this.Web.addBreadcrumb(this.Trans.get('lang.home'));
                this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.caption));
                this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.manage_api.caption),{name:'manageapi.list'});
                this.Web.addBreadcrumb(this.title);

                this.Web.setBodyWithPadding(false);
                this.Web.setShow("moduser");
            },

        }
        // destroyed () {
        //     this.Web.setBodyWithPadding(true);
        // }
    };
</script>
