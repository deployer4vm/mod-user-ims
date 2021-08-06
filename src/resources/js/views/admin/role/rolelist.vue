<template>
    <div>
        <!-- Filters -->
        <!-- <div class="ui-bordered px-4 pt-4 mb-4">
      <div class="form-row align-items-center">        
        <div class="col-md mb-4">
          <label class="form-label">Role</label>
          <b-select v-model="filterRole" :options="['Any', 'SPV', 'Manager Bagian', 'Manager Utama', 'Enjin']" />
        </div>
        <div class="col-md mb-4">
          <label class="form-label">Status</label>
          <b-select v-model="filterStatus" :options="['Any', 'Active', 'Banned', 'Deleted']" />
        </div>
        <div class="col-md col-xl-2 mb-4">
          <label class="form-label d-none d-md-block">&nbsp;</label>
          <b-btn variant="secondary" :block="true">Show</b-btn>
        </div>
      </div>
    </div> -->
        <!-- / Filters -->
        <header-breadcrumb :pageTitle="pageTitle" :showBack="false" />
        <b-card class="m-3" no-body>
            <!-- Table controls -->
            <b-card-body class="pt-3 pb-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <b-form-group :label="Trans.get('pagination.per_page')" class="d-inline-block w-auto mt-1">
                            <b-select v-model="perPage" :options="[10, 20, 30, 40, 50]"/>
                        </b-form-group>
                        <b-form-group :label="Trans.get('lang.search')" class="d-inline-block w-auto mt-1">
                            <b-input placeholder="Search..." v-model="searchString" />
                        </b-form-group>     
                        <b-btn variant="info" @click="doSearch" style="margin-top: -3px;" class="d-inline-block w-auto">
                            <span class="ion ion-ios-search"></span>
                        </b-btn>
                    </div>
                    <div>
                        <router-link v-if="UserAuth.hasAccess(accessRuleKey, 'c')" class="btn btn-success d-block" :to="{ name: 'role.add' }">
                            <span class="ion ion-md-add"></span>&nbsp; {{ Trans.get("role.rolelist.add_new_role") }}
                        </router-link>
                    </div>
                </div>
            </b-card-body>
            <!-- / Table controls -->

            <!-- Table -->
            <hr class="border-light m-0" />

            <div class="table-responsive">
                <b-table :items="listData.data" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :striped="true" :bordered="true" class="card-table">
                    <template v-slot:cell(tenant)="data">
                        {{ data.item.tenant ? data.item.tenant.name : "" }}
                    </template>

                    <template v-slot:cell(tenant_group)="data">
                        {{ data.item.tenant_group ? data.item.tenant_group.name : "" }}
                    </template>

                    <template v-slot:cell(role_code)="data">
                        <b-badge variant="outline-default">{{ data.item.role_code }}</b-badge>
                    </template>

                    <template v-slot:cell(actions)="data">
                        <!-- <b-btn variant="default btn-xs icon-btn md-btn-flat" v-b-tooltip.hover title="Edit"><i class="ion ion-md-create"></i></b-btn> -->
                        <router-link class="btn btn-success icon-btn btn-sm md-btn-flat" :title="Trans.get('lang.edit')" :to="{ name: 'role.edit', params: { roleId: data.item.id } }" v-if="UserAuth.hasAccess(accessRuleKey, 'u')">
                            <span class="ion ion-md-create"></span>
                        </router-link>

                        <b-btn class="btn btn-danger icon-btn btn-sm md-btn-flat" :title="Trans.get('lang.delete')" @click="deleteRole(data.item.id)" v-if="UserAuth.hasAccess(accessRuleKey, 'd')">
                            <span class="ion ion-md-close"></span>
                        </b-btn>
                        <!-- <b-dropdown variant="default btn-xs icon-btn md-btn-flat hide-arrow" :right="!isRTL">
                        <template slot="button-content">
                            <i class="ion ion-ios-settings"></i>
                        </template>
                        <b-dropdown-item @click="deleteRole(data.item.id)">Remove</b-dropdown-item>
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

    export default {
        name: "pages-role-list",
        metaInfo() {
            return { title: this.pageTitle };
        },
        components: {
            flatPickr,
        },
        data() {
            return {
                accessRuleKey: "moduser.role",
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
            };
        },

        computed: {
            listData: {
                get() {
                    return this.$store.state.role.roleList;
                },
                set(value) {
                    this.$store.commit("role/setRoleList", value);
                },
            },
            pageTitle() {
                return this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.role.caption);
            },
            listRole() {
                return this.$store.state.role.roleList;
            },
            totalItems() {
                return this.usersData.length;
            },
            totalPages() {
                return Math.ceil(this.listData.count / this.perPage);
            },
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
            // searchString(v) {
            //     const val = v.toLowerCase();
            //     var that = this;
            //     clearTimeout(this.suggestTimeout);
            //     this.suggestTimeout = setTimeout(function() {
            //         that.loadData(1, val);
            //     }, 300);
            // },
        },
        methods: { 
            doSearch() {
                this.loadList(this.curPage,this.searchString,this.sortBy,this.sortDesc);
            },   
            loadData(curPage, q = "", orderBy = false, sortDesc = false) {
                var offset = this.perPage * (curPage - 1);
                this.loadParams = {};

                this.loadParams.limit = this.perPage;
                this.loadParams.offset = offset;

                if (q != "") {
                    this.loadParams.q = q;
                }

                if (orderBy != false) {
                    this.loadParams.orderBy = orderBy;
                    this.loadParams.orderType = sortDesc ? "DESC" : "ASC";
                }

                if (this.filterRole != "all") {
                    this.loadParams.role_code = this.filterRole;
                }

                if (this.filterStatus != "all") {
                    this.loadParams.status = this.filterStatus;
                }

                this.$store.dispatch("role/roleList", this.loadParams);
                // .then((res)=>{
                //     _.forEach(res.data,(v,i)=>{
                //         v.roles = v.role.split(';');
                //     });
                //     this.listData.data = res.data;
                //     // this.$store.commit("user/setUserList", this.listData);
                //     // console.log('data : ',this.listData);
                // });
            },
            deleteRole(roleId) {
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
                            .dispatch("role/delete", roleId)
                            .then((res) => {
                                this.Web.showAlert({ text: "Data deleted" });
                                this.loadData(1);
                            })
                            .catch((res) => {
                                this.Web.showAlert({ text: "Delete fail", style: "warning" });
                            });
                    },
                });
            },
            initView() {
                this.Web.setModule("moduser");

                this.Web.setNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.caption));
                // this.Web.appendNavbarTitle(this.Trans.chose(this.AppConfig.packageLocal.PSBBI.access.children.module.caption));

                this.Web.resetBreadcrumb();
                this.Web.addBreadcrumb(this.Trans.get('lang.home'));
                this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.caption));
                this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.moduser.access.children.role.caption));
                // this.Web.addBreadcrumb(this.Trans.chose(this.AppConfig.packageLocal.PSBBI.access.children.merchant.children.insurance.caption));
                
                this.Web.setBodyWithPadding(false);
                this.Web.setShow("moduser");
            },
        },
        created() {
            if (!this.UserAuth.hasAccess(this.accessRuleKey)) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({ text: this.Trans.get("alert.access_denied"), style: "warning" });
                return false;
            }
            this.initView();

            this.loadData(1);
            this.fields = [
                { key: "id", sortable: true, tdClass: "align-middle" },
                { key: "name", label: this.Trans.get("role.field_caption.name"), sortable: true, tdClass: "align-middle" },
                { key: "level", label: this.Trans.get("role.field_caption.level"), sortable: true, tdClass: "align-middle" },
                { key: "tenant", label: this.Trans.get("role.field_caption.tenant"), sortable: true, tdClass: "align-middle" },
                { key: "tenant_group", label: this.Trans.get("role.field_caption.tenant_group"), sortable: true, tdClass: "align-middle" },
                { key: "role_code", sortable: true, tdClass: "align-middle" },
                {
                    key: "actions",
                    label: " ",
                    tdClass: "text-nowrap align-middle text-center",
                },
            ];

            if (!(this.UserAuth.hasAccess(this.accessRuleKey, "u") || this.UserAuth.hasAccess(this.accessRuleKey, "d"))) {
                this.fields.splice(5, 1);
            }
            //jika tidak menggunakan system tenant maka hilangkan kolom tenant
            if (this.AppConfig.system.multitenant.active == 0) {
                this.fields.splice(3, 2);
            } else {
                if (this.AppConfig.packageLocal.moduser.role_hidden_field.includes("tenant_group")) {
                    this.fields.splice(4, 1);
                }
                if (this.AppConfig.packageLocal.moduser.role_hidden_field.includes("tenant")) {
                    this.fields.splice(3, 1);
                }
                if (this.AppConfig.packageLocal.moduser.role_hidden_field.includes("level")) {
                    this.fields.splice(2, 1);
                }
            }
        },
    };
</script>
