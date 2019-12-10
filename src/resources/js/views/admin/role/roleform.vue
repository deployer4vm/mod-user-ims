<template>
    <div>
        
        <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
            <router-link class="btn btn-outline-success d-block" :to="{name: 'role.list'}">
                <span class="ion ion-ios-arrow-back"></span>&nbsp; {{Trans.get('lang.back')}}
            </router-link>
            <div>
                <span class="text-muted font-weight-light">{{Trans.get('role.module_caption')}} /</span>
                {{ title }}
            </div>
        </h4>

        <b-card>
            <b-card-body class="pb-2">

                <b-form-group :label="Trans.get('role.field_caption.name')" class="col position-relative">
                    <b-input 
                        :state="$v.roleForm.name.$error?'invalid':''" 
                        v-model="roleForm.name" @blur="formatRoleCodeFromRoleName"
                    />
                    <invalid-tooltip :inputItem="$v.roleForm.name" :fieldName="Trans.get('role.field_caption.name')" />
                </b-form-group>

                <b-form-group :label="Trans.get('role.field_caption.role_code')" class="col position-relative">
                    <b-input 
                        :state="$v.roleForm.role_code.$error?'invalid':''"
                        v-model="roleForm.role_code" @blur="formatRoleCode"
                    />
                    <invalid-tooltip :inputItem="$v.roleForm.role_code" :fieldName="Trans.get('role.field_caption.role_code')" />
                </b-form-group>

                <b-form-group :label="Trans.get('role.field_caption.tenant_group')"  class="col position-relative">
                    <b-select 
                        :state="$v.roleForm.tenant_group_id.$error?'invalid':''" 
                        v-model="roleForm.tenant_group_id" 
                        :options="tenantGroupOption"
                        
                    />
                    <invalid-tooltip :inputItem="$v.roleForm.tenant_group_id" :customAlert="{'minValue': 'validation.required'}" :fieldName="Trans.get('role.field_caption.tenant_group')" />
                </b-form-group>

                
                <b-form-group :label="Trans.get('role.field_caption.level')"  class="col position-relative">
                    <b-select 
                        :state="$v.roleForm.level.$error?'invalid':''" 
                        v-model="roleForm.level" 
                        :options="levelOption" 
                    />
                    <invalid-tooltip :inputItem="$v.roleForm.level" :fieldName="Trans.get('role.field_caption.level')" />
                </b-form-group>

            </b-card-body>
      
            <hr class="border-light m-0">

            <div class="table-responsive">

                <table class="table mb-0 table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{Trans.get('role.roleform.module')}}</th>
                            <th>{{Trans.get('role.roleform.feature')}}</th>
                            <th>{{Trans.get('role.roleform.create')}}</th>
                            <th>{{Trans.get('role.roleform.read')}}</th>
                            <th>{{Trans.get('role.roleform.update')}}</th>
                            <th>{{Trans.get('role.roleform.delete')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(moduleRule,key,i) in AppConfig.acl">                    
                            <tr class="table-primary">
                                <th scope="row"><b>{{i+1}}</b></th>
                                <td>
                                    {{Trans.chose(moduleRule.acl_caption)}}
                                    <div v-if="moduleRule.acl_description!=''"><i>{{Trans.chose(moduleRule.acl_description)}}</i></div>
                                </td>
                                <td colspan="5">                            
                                    <div class="float-right">
                                        <b-btn variant="primary" size="xs" :disabled="disabledModuleAccess[key]" @click="checkModuleAll(key)">{{Trans.get('role.roleform.check_all')}}</b-btn> 
                                        <b-btn variant="success" size="xs" :disabled="disabledModuleAccess[key]" @click="uncheckModuleAll(key)">{{Trans.get('role.roleform.uncheck_all')}}</b-btn>
                                    </div>             
                                    <div class="float-left">
                                        <b-check  :value="1" :unchecked-value="0" v-model="roleForm.rule[key].has_access" @change="setModule(key,$event)">
                                            {{Trans.get('role.roleform.has_access')}}
                                        </b-check>
                                    </div>
                                </td>
                            </tr>
                            <template v-for="(rule,ruleKey,index) in moduleRule.children">
                                <tr>
                                    <th scope="row">{{index+1}}</th>
                                    <td></td>
                                    <td :class="'rulekey-' + dotCount(ruleKey)">
                                        {{Trans.chose(rule.acl_caption)}}
                                        <div v-if="rule.acl_description!=''"><i>{{Trans.chose(rule.acl_description)}}</i></div>
                                    </td> 
                                    <template v-if="rule.crud.c!=1&&rule.crud.r!=1&&rule.crud.u!=1&&rule.crud.d!=1">                                   
                                        <td colspan="4">
                                            <b-check :value="1" :unchecked-value="0" v-model="roleForm.rule[ruleKey].has_access"
                                            :disabled="disabledModuleAccess[key]">
                                                {{Trans.get('role.roleform.has_access')}}
                                            </b-check>
                                        </td>
                                    </template>
                                    <template v-else> 
                                        <td><b-check :value="1" :unchecked-value="0" v-model="roleForm.rule[ruleKey].c" v-if="rule.crud.c==1" :disabled="disabledModuleAccess[key]" class="px-2 m-0" /></td>
                                        <td><b-check :value="1" :unchecked-value="0" v-model="roleForm.rule[ruleKey].r" v-if="rule.crud.r==1" :disabled="disabledModuleAccess[key]" class="px-2 m-0" /></td>
                                        <td><b-check :value="1" :unchecked-value="0" v-model="roleForm.rule[ruleKey].u" v-if="rule.crud.u==1" :disabled="disabledModuleAccess[key]" class="px-2 m-0" /></td>
                                        <td><b-check :value="1" :unchecked-value="0" v-model="roleForm.rule[ruleKey].d" v-if="rule.crud.d==1" :disabled="disabledModuleAccess[key]" class="px-2 m-0" /></td>
                                    </template>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>

            </div>
      
            <div class="text-right mt-3">
                <b-btn variant="primary" @click="save">{{Trans.get('lang.save_change')}}</b-btn>
            </div>
        </b-card>
    </div>
</template>
<!-- Page -->
<style src="@/vendor/styles/pages/users.scss" lang="scss"></style>
<style scoped>
    .rulekey-2 {
        padding-left: 40px;
    }
    .rulekey-3 {
        padding-left: 80px;
    }
    .rulekey-4 {
        padding-left: 120px;
    }
    .rulekey-5 {
        padding-left: 160px;
    }
    .rulekey-6 {
        padding-left: 200px;
    }
</style>
<script>
import { required, minLength, minValue } from "node_modules/vuelidate/lib/validators";

export default {
    name: 'pages-role-form',
    data: () => ({
        permissions: [],//data acl
        roleForm: {
            id: 0,
            name: '',
            role_code: '',
            level: 2,
            tenant_group_id: 0,
            tenant_id: 0,
            rule: {}
        },
        disabledModuleAccess: {},
        rulePerModule: {},
        levelOption: [],
        tenantOption: [],
        tenantGroupOption: []
    }),
    validations() {
        return {
            roleForm: {
                name: {
                    required,
                    minLength: minLength(5)
                },
                role_code: {
                    required,
                    minLength: minLength(5)
                },
                tenant_group_id: {
                    required,
                    minValue: minValue(1)
                },
                level: {
                    required
                }
            }
        };
    },
    computed: {
        isAdd() {
            return this.$route.params.roleId ? false : true;
        },   
        title() {
            return this.isAdd ? this.Trans.get('role.roleform.form_add_caption') : "#" + this.roleForm.name;
        },
        tenantGroup() {
            return this.$store.state.tenant.listTenantGroup;
        }
    },
    methods: {
        dotCount(key) {
            return (key.split(".").length - 1);
        },
        setEmptyRole() {
            let tmp = {};
            _.forEach(this.AppConfig.acl,(v,k) => {
                tmp[k] = {'has_access':0};

                this.disabledModuleAccess[k] = true;
                this.rulePerModule[k] = {}
                _.forEach(v.children,(rule,ruleKey) => {
                    tmp[ruleKey] = {
                            "has_access":0,
                            "c": 0,
                            "r": 0,
                            "u": 0,
                            "d": 0
                        };

                    this.rulePerModule[k][ruleKey] = ruleKey;
                });
            });
            this.roleForm.rule = tmp;
        },
        loadRole() {  
            this.$store.dispatch(
                    'role/getRole',this.$route.params.roleId                    
                ).then((res)=>{

                    _.forEach(res,(v,k) => {
                        if(k!='rule')this.roleForm[k] = v;
                    });

                    let tmp = JSON.parse(JSON.stringify(this.roleForm.rule));

                    if(res.rule!=null){
                        _.forEach(this.roleForm.rule,(v,k) => {
                            if(res.rule[k]!=undefined){
                                tmp[k] = res.rule[k];
                                if(tmp[k]['has_access'])
                                    this.disabledModuleAccess[k] = false;
                                _.forEach(res.rule[k],(v2,k2) => {
                                    tmp[k][k2] = v2?1:0;
                                });
                            }
                        });
                    }

                    this.roleForm.rule = tmp;

                }).catch((res)=>{
                    console.log('get role error : ',res);
                    this.Web.showAlert({text: "Get role Error",style: "warning"});
                });
        },
        setModule(moduleKey,val){
            let tmp = JSON.parse(JSON.stringify(this.disabledModuleAccess));
            tmp[moduleKey] = val?true:false;
            this.disabledModuleAccess = tmp;
        },
        //ceklis semua rule di module tertentu
        checkModuleAll(moduleKey){
            let tmp = JSON.parse(JSON.stringify(this.roleForm.rule));
            _.forEach(this.rulePerModule[moduleKey],(v2,k2)=>{
                tmp[k2] = {
                    "has_access":1,
                    "c": 1,
                    "r": 1,
                    "u": 1,
                    "d": 1
                };
                _.forEach(this.AppConfig.acl[moduleKey]['children'][k2]['crud'],(v3,k3)=>{                    
                    if(v3==0||v3==0)tmp[k2][k3]=0;
                });
            });
            this.roleForm.rule = tmp;
        },
        //unceklis semua rule di module tertentu
        uncheckModuleAll(moduleKey){
            let tmp = JSON.parse(JSON.stringify(this.roleForm.rule));
            _.forEach(this.rulePerModule[moduleKey],(v2,k2)=>{
                tmp[k2] = {
                    "has_access":0,
                    "c": 0,
                    "r": 0,
                    "u": 0,
                    "d": 0
                };
            });
            this.roleForm.rule = tmp;
        },
        formatRoleCodeFromRoleName(v){
            this.$v.roleForm.name.$touch();
            if(this.roleForm.role_code==''){
                this.roleForm.role_code = this.roleForm.name.toLowerCase().replace(/[^a-zA-Z0-9_]/g, "_");
            }
        },
        formatRoleCode(v){
            this.$v.roleForm.role_code.$touch();
            this.roleForm.role_code = this.roleForm.role_code.replace(/[^a-zA-Z0-9_]/g, "_");
        },
        save() {
            _.forEach(this.disabledModuleAccess,(v,k) => {
                _.forEach(this.rulePerModule[k],(v2,k2)=>{
                    //jika disabled maka uncheck semua hak akses nya
                    if(v){
                        this.roleForm.rule[k2] = {
                            "has_access":0,
                            "c": 0,
                            "r": 0,
                            "u": 0,
                            "d": 0
                        };
                    }else{
                        this.roleForm.rule[k2]['has_access'] = 
                            this.roleForm.rule[k2]['has_access'] || 
                            this.roleForm.rule[k2]['c'] ||
                            this.roleForm.rule[k2]['r'] ||
                            this.roleForm.rule[k2]['u'] ||
                            this.roleForm.rule[k2]['d']?1:0;
                    }
                });
            });

            this.$v.$touch();  
            if (this.$v.$invalid) {
                this.Web.showAlert({
                    type: 'danger', 
                    title: this.Trans.get('alert.form_must_complete_title'),
                    text: this.Trans.get('alert.form_must_complete_text') 
                });
            }else{

                if(this.isAdd){
                    this.$store.dispatch("role/create", this.roleForm).then((res)=>{
                        this.Web.showAlert({type: 'info', text: 'Role Registered Successfully' });
                        this.$router.push({name: 'role.list'});
                    }).catch((err)=>{
                        console.log('create role error : ',err);
                        this.Web.showAlert({type: 'danger', text: 'Save data failed' });
                    });
                }else{

                    this.$store.dispatch("role/update", {data: this.roleForm,id: this.roleForm.id}).then((res)=>{
                        this.Web.showAlert({type: 'info', text: 'Role Updated Successfully' });
                        this.$router.push({name: 'role.list'});
                    }).catch((err)=>{
                        console.log('update role error : ',err);
                        this.Web.showAlert({type: 'danger', text: 'Save data failed' });
                    });
                }
                
            } 
        }
    },
    created() {
        this.setEmptyRole();

        if(!this.isAdd){
            this.loadRole();
        }
        
        let startI = this.UserAuth.getUser('level') + 1;
        let endI = this.AppConfig.packageLocal.moduser.user_role.user_level.max+1;
        if(startI < this.AppConfig.packageLocal.moduser.user_role.user_level.min) startI = this.AppConfig.packageLocal.moduser.user_role.user_level.max;
        //load level
        for (let i = startI; i < endI; i++) {
            this.levelOption[i] = i;
        };

        //load tenant group jika fitur multitenant aktif
        if(this.AppConfig.system.web_admin.multitenant.active==1){
            this.$store.dispatch('listTenantGroup').then((res)=>{
                let tmp = {};
                _.forEach(res,(v,k)=>{
                    tmp[v.id] = v.name;
                });
                this.tenantGroupOption  = tmp;
            });
            if(this.AppConfig.packageLocal.moduser.role_hidden_field.includes('tenant_group')){
                this.showTenantGroup = false;
            } 
        }else{
            this.showTenantGroup = false;
        }
        
    }
}
</script>
