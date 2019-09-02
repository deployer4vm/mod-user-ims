<template>
  <div>

    <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
      <div>Users</div>
      <router-link class="btn btn-success rounded-pill btn-sm d-block" :to="{name: 'user.add'}">
        <span class="ion ion-md-add"></span>&nbsp; Tambah User
      </router-link>
    </h4>

    <!-- Filters -->
    <div class="ui-bordered px-4 pt-4 mb-4">
      <div class="form-row align-items-center">        
        <div class="col-md mb-4">
          <label class="form-label">Role</label>
          <b-select v-model="filterRole" :options="roleItems" />
        </div>
        <div class="col-md mb-4">
          <label class="form-label">Status</label>
          <b-select v-model="filterStatus" :options="{'all':'Any','1':'Active','2':'Banned'}" />
        </div>
        <!-- <div class="col-md col-xl-2 mb-4">
          <label class="form-label d-none d-md-block">&nbsp;</label>
          <b-btn variant="secondary" :block="true">Show</b-btn>
        </div> -->
      </div>
    </div>
    <!-- / Filters -->

    <b-card no-body>
      <!-- Table controls -->
      <b-card-body>
        <div class="row">
          <div class="col">
            Per page: &nbsp;
            <b-select
              size="sm"
              v-model="perPage"
              :options="[10, 20, 30, 40, 50]"
              class="d-inline-block w-auto"
            />
          </div>
          <div class="col">
            <b-input
              size="sm"
              placeholder="Search..."
              class="d-inline-block w-auto float-sm-right"
              v-model="searchString"
            />
          </div>
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
          :current-page="1"
          :per-page="perPage"
          class="card-table"
        >
          <template slot="account" slot-scope="data">
            <a href="javascript:void(0)">{{data.item.account}}</a>
          </template>

          <template slot="verified" slot-scope="data">
            <span
              class="ion"
              :class="{'ion-md-checkmark text-primary': data.item.verified, 'ion-md-close text-light': !data.item.verified}"
            ></span>
          </template>

          <template slot="role" slot-scope="data">
            <b-badge variant="outline-default" v-for="(dRole) in data.item.roles" v-if="dRole!=''" :key="data.item.id+dRole">{{ dRole }}</b-badge>
          </template>

          <template slot="status" slot-scope="data">
            <b-badge variant="outline-success" v-if="data.item.status === 1 || data.item.status === 0">Active</b-badge>
            <b-badge variant="outline-danger" v-if="data.item.status === 2">Banned</b-badge>
            <!-- <b-badge variant="outline-default" v-if="data.item.status === 0">Guest</b-badge> -->
          </template>

          <template slot="actions" slot-scope="data">
                <!-- <b-btn variant="default btn-xs icon-btn md-btn-flat" v-b-tooltip.hover title="Edit"><i class="ion ion-md-create"></i></b-btn> -->
                <router-link
                    class="btn btn-default icon-btn btn-xs md-btn-flat"
                    title="Edit"
                    v-b-tooltip.hover
                    :to="{name: 'user.edit', params: {userId: data.item.id}}"
                >
                    <span class="ion ion-md-create"></span>
                </router-link>
                <b-btn
                    class="btn btn-danger icon-btn btn-xs md-btn-flat"
                    title="Delete"
                     @click="deleteUser(data.item.id)"
                    v-b-tooltip.hover
                >
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
                        <span class="text-muted" v-if="listData.count">Page {{ curPage }} of {{ totalPages }}</span>
                    </div>
                    <div class="col-sm pt-3">
                        <b-pagination
                        class="justify-content-center justify-content-sm-end m-0"
                        v-if="listData.count"
                        v-model="curPage"
                        :total-rows="listData.count"
                        :per-page="perPage"
                        size="sm"
                        />
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
    name: "pages-user-list",
    metaInfo: {
        title: "User list"
    },
    components: {
        flatPickr
    },
    data: () => ({
        
        // START ----FI listing option
        sortBy: "id",
        sortDesc: false,
        perPage: 10,
        curPage: 1,
        searchString: '',
        loadParams: {},
        roleItems:{},
        filterRole:'all',
        filterStatus:'all',
        // END ---- listing option
        fields:[],
        defaultFields: [
            { key: "id", sortable: true, tdClass: "align-middle" },
            { key: "username", sortable: true, tdClass: "align-middle" },
            { key: "email", sortable: true, tdClass: "align-middle" },
            { key: "name", sortable: true, tdClass: "align-middle" },
            { key: "role", sortable: true, tdClass: "align-middle" },
            { key: "status", sortable: true, tdClass: "align-middle" },
            {
                key: "actions",
                label: " ",
                tdClass: "text-nowrap align-middle text-center"
            }
        ]
    }),

    computed: {
        listData: {
            get() {
                return this.$store.state.user.userList;
            },
            set(value) {
                this.$store.commit("user/setUserList", value);
            }
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
            this.loadData(v,this.searchString,this.sortBy,this.sortDesc);
        },
        perPage(v) {
            this.loadData(this.curPage,this.searchString,this.sortBy,this.sortDesc);
        },
        sortBy(v) {
            this.loadData(this.curPage,this.searchString,v,this.sortDesc);
        },
        sortDesc(v) {
            this.loadData(this.curPage,this.searchString,this.sortBy, v);
        },   
        filterRole(v) {
            this.loadData(this.curPage,this.searchString,this.sortBy,this.sortDesc);
        },  
        filterStatus(v) {
            this.loadData(this.curPage,this.searchString,this.sortBy,this.sortDesc);
        }, 
        searchString(v) {
            const val = v.toLowerCase();      
            var that = this;
            clearTimeout(this.suggestTimeout);
            this.suggestTimeout = setTimeout(function(){
                that.loadData(1,val);      
            },300);
        } 
    },
    methods: {
        loadData(curPage,q='',orderBy=false,sortDesc=false){
            var offset = (this.perPage * (curPage-1));
            this.loadParams = {};

            this.loadParams.limit = this.perPage;
            this.loadParams.offset = offset;

            if(q!=''){
                this.loadParams.q = q;
            }

            if(orderBy!=false){
                this.loadParams.orderBy = orderBy;
                this.loadParams.orderType = sortDesc?'DESC':'ASC';
            }

            if(this.filterRole!='all'){
                this.loadParams.role_code = this.filterRole;
            }

            if(this.filterStatus!='all'){
                this.loadParams.status = this.filterStatus;
            }

            this.$store.dispatch("user/userList", this.loadParams);
            // .then((res)=>{
            //     _.forEach(res.data,(v,i)=>{
            //         v.roles = v.role.split(';');
            //     });
            //     this.listData.data = res.data;
            //     // this.$store.commit("user/setUserList", this.listData);
            //     // console.log('data : ',this.listData);
            // });
        },
        setStatus(userId, status) {
            if(status==1){

            }else{

            }
        },
        deleteUser(userId) {            
            this.Web.showAlert({
                styleType: "modal",
                style: "warning",
                title: "Delete Confirmation",
                text: "Are you sure ?",
                modalButtonCancel: "No",
                modalButtonOk: "Yes",
                onOk:()=>{
                this.$store.dispatch('user/delete',userId).then((res)=>{
                    this.Web.showAlert({text: "Data deleted"});
                    this.loadData(1);
                }).catch((res)=>{
                    this.Web.showAlert({text: "Delete fail",style: "warning"});
                });
                }
            });
        }
    },
    created() {
        //load data user
        this.loadData(1); 

        //load data role
        this.$store.dispatch("role/roleList").then((res)=>{
            let tmpRoleItems = {'all':'Any'};
            _.forEach(res.data,(v,i)=>{
                tmpRoleItems[v.role_code] = v.name;
            })
            this.roleItems = tmpRoleItems;
        });


        this.fields = JSON.parse(JSON.stringify(this.defaultFields));

        //jika username termasuk dari field yang dihide maka hide kolomnya
        if(this.AppConfig.packageLocal.moduser.users_hidden_field.includes('username')){
            this.fields.splice(1,1);
        }
        //filter kolom table user berdasarkan konfig
        // _.forEach(this.AppConfig.packageLocal.moduser.users_hidden_field,(v,i)=>{
        //     this.fields.splice(5,0,{ key: "stok_optimum", label:"Stok Optimum", sortable: true, thStyle: "min-width: 5rem"})
        // });
    }
};
</script>