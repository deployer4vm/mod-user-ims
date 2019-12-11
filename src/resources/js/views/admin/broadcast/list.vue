<template>
  <div>
    <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
      <div>Prk</div>
      <b-btn variant="success" @click="showForm()" class="d-block" v-if="UserAuth.hasAccess('moduser.broadcast','c')">
        <span class="ion ion-md-add"></span>&nbsp; Tambah Broadcast
      </b-btn>
    </h4>

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
              @input="filter($event)"
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
          :current-page="curPage"
          :per-page="perPage"
          class="card-table"
        >
        
          <template slot="nilai_pagu" slot-scope="data">
            {{Format.formatPrice(data.item.nilai_pagu)}}
          </template>
        
          <template slot="nilai" slot-scope="data">
            {{Format.formatPrice(data.item.nilai)}}
          </template>

          <template slot="actions" slot-scope="data">
            <b-btn @click="showForm(false,data.item.id)" variant="default btn-xs icon-btn md-btn-flat" v-b-tooltip.hover title="Edit" v-if="UserAuth.hasAccess('moduser.broadcast','u')">
              <i class="ion ion-md-create"></i>
            </b-btn>
            <b-btn @click="deleteData(data.item.id)" variant="default btn-xs icon-btn md-btn-flat" v-b-tooltip.hover title="Remove" v-if="UserAuth.hasAccess('moduser.broadcast','d')">
              <i class="ion ion-md-close"></i>
            </b-btn>
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

      <!-- Modal template -->
      <b-modal id="modals-form" :size="defaultModalSize" @ok="formSubmitted" @hidden="$v.$reset" >
        <div slot="modal-title">
          Form / <span class="font-weight-light">Broadcast</span><br>
        </div>

        <b-form-row>
          <b-form-group label="Judul" class="col position-relative">
            <b-input :state="$v.form.judul.$error?'invalid':''" v-model.trim="form.judul" @blur="$v.form.judul.$touch()" placeholder="Judul" />
            <invalid-tooltip :inputItem="$v.form.judul" :fieldName="'Judul'" />
          </b-form-group>          
        </b-form-row>

        <b-form-row>
          <b-form-group label="Isi Pesan / Notifikasi" class="col position-relative">              
            <b-textarea :state="$v.form.message.$error?'invalid':''" v-model.trim="form.message" @blur="$v.form.no_prk.$touch()" placeholder="No PRK" rows="5" />
            <invalid-tooltip :inputItem="$v.form.message" :fieldName="'Pesan Notifikasi'" />
          </b-form-group>        
        </b-form-row>

      </b-modal>

  </div>
</template>

<script>
import MaskedInput, {conformToMask} from 'node_modules/vue-text-mask';
import * as textMaskAddons from 'node_modules/text-mask-addons/dist/textMaskAddons'
import { required } from "node_modules/vuelidate/lib/validators";
export default {
  name: "master-Prk-list",
  validations: {
    form: {
      judul: {
        required
      },
      no_prk: {
        required
      },
      no_wbs: {
        required
      },
      tahun: {
        required
      }
    }
  },
  components: {
    MaskedInput
  },
  data: () => ({
    yearMask: [/\d/, /\d/, /\d/, /\d/],
    isAdd: true,
    defaultModalSize: 'sm',
    // Options
    searchKeys: ['judul','no_prk','no_wbs'],
    sortBy: "id",
    sortDesc: false,
    perPage: 10,
    curPage: 1,

    fields: [
      { key: "date", sortable: true },
      { key: "title", sortable: true },
      { key: "message", sortable: true },
      { key: "actions", label: " ", tdClass: "text-center text-nowrap" }
    ],

    listDataOriginal: [],
    form: {
      id:0,
      judul: '',
      tahun: '',
      no_prk: '',
      no_wbs: '',
      nilai: ''
    }
  }),

  computed: {
    formTitle() {
      return this.isAdd?'Tambah Baru':'Edit';
    },
    listData() {
      return this.$store.state.master.listPrk;
    },
    oneData() {
      return this.$store.state.master.prk;
    },
    totalPages() {
      return Math.ceil(this.listData.count / this.perPage);
    }
  },

    methods: {
        filter(value) {
            const val = value.toLowerCase();
            const filtered = this.listDataOriginal.data.filter(d => {
                return (
                Object.keys(d)
                    .filter(k => this.searchKeys.includes(k))
                    .map(k => String(d[k]))
                    .join("|")
                    .toLowerCase()
                    .indexOf(val) !== -1 || !val
                );
            });
            this.listData.data = filtered;
        },    
        access(key) {
            return this.UserAuth.hasAccess('moduser.broadcast',key);
        },
        showForm(isAdd=true,id=0) {
            this.isAdd = isAdd;
            if(!isAdd){        
                this.$store.dispatch('master/getPrk',id).then((res)=>{
                
                this.form.id = this.oneData.id;
                this.form.judul = this.oneData.judul;
                this.form.tahun = String(this.oneData.tahun);
                this.form.no_prk = this.oneData.no_prk;
                this.form.no_wbs = this.oneData.no_wbs;
                this.form.nilai = this.Format.formatPrice(this.oneData.nilai);

                this.$bvModal.show("modals-form");

                }).catch((res)=>{
                console.log('get Prk error : ',res)
                this.Web.showAlert({text: "Get Data Error",type: "warning"});
                });
            }else{        

                this.form.id = 0;
                this.form.judul = '';
                this.form.tahun = '';
                this.form.no_prk = '';
                this.form.no_wbs = '';
                this.form.nilai = '';

                this.$bvModal.show("modals-form");
            }
        },
        formSubmitted(ev) {
            ev.preventDefault();
            this.$v.$touch();
            if (this.$v.$invalid) {
                this.Web.showAlert({text: "Please fix form error",title:"Alert",type: "warning"});
                return false;
            }
            if(this.isAdd){
                this.saveData({
                judul: this.form.judul,
                tahun: this.form.tahun,
                no_prk: this.form.no_prk,
                no_wbs: this.form.no_wbs,
                nilai: this.form.nilai.replace(/\D+/g, '')
                });
            }else{
                this.updateData({
                id: this.form.id,          
                judul: this.form.judul,
                tahun: this.form.tahun,
                no_prk: this.form.no_prk,
                no_wbs: this.form.no_wbs,
                nilai: this.form.nilai.replace(/\D+/g, '')
                });
            }
        },
        saveData(data) {
            if(!this.UserAuth.hasAccess('moduser.broadcast','c')) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({text: this.Trans.get('alert.access_denied'),style: "warning"});
                return false;
            }
            this.$store.dispatch('master/createPrk',data).then((res)=>{
                this.Web.showAlert({text: "Data saved"});
                this.$bvModal.hide("modals-form");
            }).catch((res)=>{
                this.Web.showAlert({text: "Save data failed",type: "warning"});
            });
        },
        updateData(data) {
            if(!this.UserAuth.hasAccess('moduser.broadcast','u')) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({text: this.Trans.get('alert.access_denied'),style: "warning"});
                return false;
            }
            this.$store.dispatch('master/updatePrk',data).then((res)=>{
                this.Web.showAlert({text: "Data updated"});
                this.$bvModal.hide("modals-form");
            }).catch((res)=>{
                this.Web.showAlert({text: "Update data failed",type: "warning"});
            });
        },
        deleteData(id) {
            if(!this.UserAuth.hasAccess('moduser.broadcast','d')) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({text: this.Trans.get('alert.access_denied'),style: "warning"});
                return false;
            }
            this.Web.showAlert({
                styleType: "modal",
                style: "warning",
                title: "Delete Confirmation",
                text: "Are you sure ?",
                modalButtonCancel: "No",
                modalButtonOk: "Yes",
                onOk:()=>{
                this.$store.dispatch('master/deletePrk',id).then((res)=>{
                    this.Web.showAlert({text: "Data deleted"});
                }).catch((res)=>{
                    this.Web.showAlert({text: "Delete fail",type: "warning"});
                });
                }
            });
        }
    },
    created() {        
        
        if(!this.UserAuth.hasAccess('moduser.broadcast')) {
            //goto dashboard current tenant
            this.Web.goToCurrentTenant();
            this.Web.showAlert({text: this.Trans.get('alert.access_denied'),style: "warning"});
            return false;
        }
        
        this.$store.dispatch("master/listPrk", {}).then(res => {
            this.listDataOriginal.data = this.listData.data.slice(0);
            this.curPage = this.listData.currentPage;
        });

        if(!this.UserAuth.hasAccess('moduser.broadcast','d') && !this.UserAuth.hasAccess('moduser.broadcast','u')){
            this.fields = [
                { key: "no_prk", sortable: true },
                { key: "no_wbs", sortable: true },
                { key: "judul", sortable: true },
                { key: "tahun", sortable: true },
                { key: "nilai_pagu", sortable: true },
                { key: "nilai", label: "Sisa Nilai", sortable: true }
                // { key: "actions", label: " ", tdClass: "text-center text-nowrap" }
            ];
        }
    }
};
</script>