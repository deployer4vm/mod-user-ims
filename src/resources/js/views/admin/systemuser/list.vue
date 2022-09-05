<template>
    <div>
        <header-breadcrumb :pageTitle="pageTitle" :showBack="false" />
        <b-card class="m-3" no-body>
            <b-card-body>
                <div class="d-flex form-row flex-xl-row align-items-end">
                    <b-form-group :label="Trans.get('pagination.per_page')" class="d-inline-block col-md mt-1">
                        <b-select v-model="perPage" :options="[10, 20, 30, 40, 50]"/>
                    </b-form-group>
                    <b-form-group :label="Trans.get('user.field_caption.role')" class="d-inline-block col-md mt-1">
                        <b-select v-model="filterRole" :options="roleItems"/>
                    </b-form-group>
                    <b-form-group :label="Trans.get('user.field_caption.status')" class="d-inline-block col-md mt-1">
                        <b-select v-model="filterStatus" :options="{
                            'all': Trans.get('lang.view_all'),
                            '1': Trans.get('user.field_caption.status_item.active'),
                            '2': Trans.get('user.field_caption.status_item.banned')
                        }"/>
                    </b-form-group>
                    <b-form-group :label="Trans.get('lang.search')" class="d-inline-block col-md mt-1">
                        <b-input-group>
                            <b-input placeholder="Search..." v-model="searchString" />
                            <b-btn variant="secondary" @click="doSearch">
                                <span class="ion ion-ios-search"></span>
                            </b-btn>
                        </b-input-group>
                    </b-form-group>
                    <!-- <b-form-group :label="''" class="d-inline-block w-auto mt-1">
                    </b-form-group> -->
                </div>
            </b-card-body>
        </b-card>
        <b-card class="m-3" no-body>
            <!-- Table controls -->
            <b-card-body>
                <div class="d-flex justify-content-end">
                    <!-- <div>
                        <b-form-group :label="Trans.get('pagination.per_page')" class="d-inline-block w-auto mt-1">
                            <b-select v-model="perPage" :options="[10, 20, 30, 40, 50]"/>
                        </b-form-group>
                        <b-form-group :label="Trans.get('user.field_caption.role')" class="d-inline-block w-auto mt-1">
                            <b-select v-model="filterRole" :options="roleItems"/>
                        </b-form-group>
                        <b-form-group :label="Trans.get('user.field_caption.status')" class="d-inline-block w-auto mt-1">
                            <b-select v-model="filterStatus" :options="{
                                'all': Trans.get('lang.view_all'),
                                '1': Trans.get('user.field_caption.status_item.active'),
                                '2': Trans.get('user.field_caption.status_item.banned')
                            }"/>
                        </b-form-group>
                        <b-form-group :label="Trans.get('lang.search')" class="d-inline-block w-auto mt-1">
                            <b-input placeholder="Search..." v-model="searchString" />
                        </b-form-group>
                        <b-form-group :label="''" class="d-inline-block w-auto mt-1">
                            <b-btn variant="info" @click="doSearch" style="margin-top: -3px;">
                                <span class="ion ion-ios-search"></span>
                            </b-btn>
                        </b-form-group>
                    </div> -->
                    <router-link v-if="UserAuth.hasAccess(accessRuleKey, 'c')" class="btn btn-primary d-block" :to="{ name: 'systemuser.add' }">
                            <span class="ion ion-md-add"></span>&nbsp; {{ Trans.get("user.userlist.add_new_user") }}
                        </router-link>
                    <router-link v-if="UserAuth.hasAccess(accessRuleKey, 'r')" class="btn btn-secondary d-block mx-1" :to="{ name: 'systemuser.role' }">
                            <span class="ion ion-md-add"></span>&nbsp; {{ Trans.get("user.systemuser.role_list") }}
                        </router-link>
                </div>
            </b-card-body>
            <!-- / Table controls -->

            <!-- Table -->
            <hr class="border-light m-0" />
            <div class="table-responsive">
                <b-table
                    :items="listData.data"
                    :fields="fields"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :striped="true"
                    :bordered="true"
                    class="card-table"
                >

                    <template v-slot:cell(account)="data">
                        <a href="javascript:void(0)">{{ data.item.account }}</a>
                    </template>

                    <template v-slot:cell(actions)="data">
                        <b-btn class="btn btn-info btn-sm md-btn-flat" v-if="data.item.api_token" 
                            v-clipboard:copy="data.item.api_token.api_token" v-clipboard:success="copySuccess"
                            v-clipboard:error="copyFail"
                        >
                            Copy Token
                        </b-btn>
                        <b-btn class="btn btn-primary btn-sm md-btn-flat" v-if="!data.item.api_token"
                            @click="generateToken(data.item.id)"
                        >
                            Generate Token
                        </b-btn>
                        <!-- <b-btn variant="default btn-xs icon-btn md-btn-flat" v-b-tooltip.hover title="Edit"><i class="ion ion-md-create"></i></b-btn> -->
                        <router-link class="btn btn-success icon-btn btn-sm md-btn-flat" :title="Trans.get('lang.edit')" v-b-tooltip.hover :to="{ name: 'systemuser.edit', params: { userId: data.item.id } }" v-if="UserAuth.hasAccess(accessRuleKey, 'u')">
                            <span class="ion ion-md-create"></span>
                        </router-link>
                        <b-btn class="btn btn-danger icon-btn btn-sm md-btn-flat" :title="Trans.get('lang.delete')" @click="deleteUser(data.item.id)" v-if="UserAuth.hasAccess(accessRuleKey, 'd') && data.item.linked_id == 0" v-b-tooltip.hover>
                            <span class="ion ion-md-close"></span>
                        </b-btn>
                        <!-- <b-dropdown variant="default btn-xs icon-btn md-btn-flat hide-arrow" :right="!isRTL">
                        <template slot="button-content">
                            <i class="ion ion-ios-settings"></i>
                        </template>
                        <b-dropdown-item href="javascript:void(0)">View profile</b-dropdown-item>
                        <b-dropdown-item @click="banUser(data.item.id)">Ban user</b-dropdown-item>
                        <b-dropdown-item @click="deleteUser(data.item.id)">Remove</b-dropdown-item>
                    </b-dropdown> -->
                    </template>
                </b-table>
            </div>

            <!-- Pagination -->
            <b-card-body class="pt-0 pb-3">
                <div class="row">
                    <div class="col-sm text-sm-left text-center pt-3">
                        <span class="text-muted" v-if="listData.count">{{ Trans.get("pagination.page_of", { curPage: curPage, totalPages: totalPages }) }}</span>
                    </div>
                    <div class="col-sm pt-3">
                        <b-pagination class="justify-content-center justify-content-sm-end m-0" v-if="listData.count" v-model="curPage" :total-rows="listData.count" :per-page="perPage" size="sm" />
                    </div>
                </div>
            </b-card-body>
            <!-- / Pagination -->
        </b-card>
    </div>
</template>

<style src="@/vendor/libs/vue-flatpickr-component/vue-flatpickr-component.scss" lang="scss"></style>

<script>
    import flatPickr from "node_modules/vue-flatpickr-component";
    import VueClipboard from 'node_modules/vue-clipboard2'
    Vue.use(VueClipboard)

    export default {
        name: "pages-usersystem-list",
        metaInfo() {
            return { title: this.pageTitle };
        },
        components: {
            flatPickr
        },
        data: () => ({
            accessRuleKey: "moduser.system_user",
            // START ----FI listing option
            sortBy: "id",
            sortDesc: false,
            perPage: 10,
            curPage: 1,
            searchString: "",
            loadParams: {},
            roleItems: {},
            filterRole: "all",
            filterStatus: "all",
            // END ---- listing option
            fields: [],
            defaultFields: [
                {
                    key: "id",
                    sortable: true,
                    tdClass: "align-middle",
                    tdAttr: {
                        "data-lable": "ID"
                    }
                },
                {
                    key: "name",
                    sortable: true, tdClass: "align-middle",
                    tdAttr: {
                        "data-lable": "Name"
                    }
                },
                {
                    key: "actions",
                    label: "Aksi",
                    tdClass: "text-nowrap align-middle text-center col-action",
                    tdAttr: {
                        "data-lable": ""
                    }

                }
            ]
        }),

        computed: {
            listData: {
                get() {
                    return this.$store.state.usersystem.userList;
                },
                set(value) {
                    this.$store.commit("usersystem/setUserList", value);
                }
            },
            pageTitle() {
                return this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.system_user.caption);
            },
            listRole() {
                return this.$store.state.role.roleList;
            },
            totalItems() {
                return this.usersData.length;
            },
            totalPages() {
                return Math.ceil(this.listData.count / this.perPage);
            }
        },
        watch: {
            curPage(v) {
                this.loadData(v, this.searchString, this.sortBy, this.sortDesc);
            },
            perPage(v) {
                this.loadData(this.curPage, this.searchString, this.sortBy, this.sortDesc);
            },
            sortBy(v) {
                this.loadData(this.curPage, this.searchString, v, this.sortDesc);
            },
            sortDesc(v) {
                this.loadData(this.curPage, this.searchString, this.sortBy, v);
            },
            filterRole(v) {
                this.loadData(this.curPage, this.searchString, this.sortBy, this.sortDesc);
            },
            filterStatus(v) {
                this.loadData(this.curPage, this.searchString, this.sortBy, this.sortDesc);
            },
            searchString(v) {
                const val = v.toLowerCase();
                var that = this;
                clearTimeout(this.suggestTimeout);
                this.suggestTimeout = setTimeout(function() {
                    that.loadData(1, val);
                }, 300);
            }
        },
        methods: {
            doSearch() {
                this.loadData(this.curPage,this.searchString,this.sortBy,this.sortDesc);
            },
            loadData(curPage, q = "", orderBy = false, sortDesc = false) {
                var offset = this.perPage * (curPage - 1);
                this.loadParams = {};
                this.loadParams.params = {};

                this.loadParams.params.limit = this.perPage;
                this.loadParams.params.offset = offset;
                this.loadParams.params.system_user = 1;

                if (q != "") {
                    this.loadParams.params.q = q;
                }

                if (orderBy != false) {
                    this.loadParams.params.orderBy = orderBy;
                    this.loadParams.params.orderType = sortDesc ? "DESC" : "ASC";
                }

                if (this.filterRole != "all") {
                    this.loadParams.params.role = this.filterRole;
                }

                if (this.filterStatus != "all") {
                    this.loadParams.params.status = this.filterStatus;
                }

                this.$store.dispatch("usersystem/userList", this.loadParams);
            },
            setStatus(userId, status) {
                if (status == 1) {
                } else {
                }
            },
            deleteUser(userId) {
                if (!this.UserAuth.hasAccess(this.accessRuleKey, "d")) {
                    //goto dashboard current tenant
                    this.Web.goToCurrentTenant();
                    this.Web.showAlert({ text: this.Trans.get("alert.access_denied"), style: "warning" });
                    return false;
                }

                this.Web.showAlert({
                    styleType: "modal",
                    style: "warning",
                    title: "Delete Confirmation",
                    text: "Are you sure ?",
                    modalButtonCancel: "No",
                    modalButtonOk: "Yes",
                    onOk: () => {
                        this.$store
                            .dispatch("usersystem/delete", userId)
                            .then(res => {
                                this.Web.showAlert({ text: "Data deleted" });
                                this.loadData(1);
                            })
                            .catch(res => {
                                this.Web.showAlert({ text: "Delete fail", style: "warning" });
                            });
                    }
                });
            },
            copySuccess() {
                this.Web.showAlert({ text: "Copy success" });
                this.loadData(1);
            },
            copyFail() {
                this.Web.showAlert({ text: "Copy fail" });
            },
            generateToken(id)
            {
                this.$store.dispatch("usersystem/generateToken", id)
                    .then(res => {
                        this.Web.showAlert({ text: "Token generated" });
                        this.loadData(1);
                    })
                    .catch(res => {
                        this.Web.showAlert({ text: "Token generated fail", style: "warning" });
                    });
            },
            initView() {
                this.Web.setModule("moduser");

                this.Web.setNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.system_user.caption));
                // this.Web.appendNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.PSBBI.access.children.module.children.system_user.caption));

                this.Web.resetBreadcrumb();
                this.Web.addBreadcrumb(this.Trans.get('lang.home'));
                this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.system_user.caption));
                this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.system_user.caption));
                // this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.PSBBI.access.children.merchant.children.insurance.children.system_user.caption));

                this.Web.setBodyWithPadding(false);
                this.Web.setShow("moduser");
            }
        },
        created() {
            if (!this.UserAuth.hasAccess(this.accessRuleKey)) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({
                    title: this.Trans.get("alert.warning_title"),
                    text: this.Trans.get("alert.access_denied"),
                    type: "warning"
                });
                return false;
            }
            this.initView();

            //load data usersystem
            this.loadData(1);

            //load data role
            this.$store.dispatch("role/roleList").then(res => {
                let tmpRoleItems = { "all": this.Trans.get("lang.view_all") };
                _.forEach(res.data, (v, i) => {
                    tmpRoleItems[v.role_code] = "[" + v.role_code + "] " + v.name;
                });
                this.roleItems = tmpRoleItems;
            });

            this.fields = this.defaultFields;

            if (!(this.UserAuth.hasAccess(this.accessRuleKey, "u") || this.UserAuth.hasAccess(this.accessRuleKey, "d"))) {
                this.fields.splice(6, 1);
            }
        }
    };
</script>
