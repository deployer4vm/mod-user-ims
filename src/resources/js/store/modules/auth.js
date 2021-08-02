import globals from "@/globals";

var authPath = globals().AppConfig.endpoint.api.auth;

const state = {
    token: null, //token akses API
    userId: null,
    tenant: null,//aktif tenant
    user: null, //data komplit user
    role: null, //list role user
    role_code: null, //main / active role
    group_app: null //group app saat login, agar jike berpindah akan di logout kan
};

const getters = {
    getLoginUser(state) {
        return state.user;
    },
    isLogin(state) {
        return state.token !== null;
    },
    getAuthRole(state) {
        return state.role?state.role[state.role_code]:false;
    },
    getAuthRoleList(state) {
        return state.role?state.role:false;
    },
    roleCount(state) {
        return state.role?Object.keys(state.role).length:0;
    },
    getAuthToken(state) {
        return state.token;
    },
    getGroupApp(state) {
        return state.group_app;
    }
};

const mutations = {
    /*
        params 
            userData : object
                token
                user
                role
                role_code : active / main role code
    */
    setLogin(state, userData) {
        state.token = userData.token;
        state.user = userData.user;
        state.userId = userData.user.id;
        state.role = userData.role;
        state.tenant = userData.tenant;
        state.role_code = userData.role_code;
    },
    setActiveRoleCode(state, roleCode) {
        state.role_code = roleCode;
    },
    setAuthData(state, userData) {
        eval('state.' + userData.key + ' = userData.value;');
    },
    setLogout(state) {
        state.token = null;
        state.userId = null;
        state.user = null;
        state.role = null;
        state.tenant = null;
        state.role_code = null;
        state.group_app = null;
    },
    setGroupApp(state, groupApp) {        
        state.group_app = groupApp;
    }
};

const actions = {
    login({ commit, dispatch, state }, authData) {
        return globals().LocalApi
            .post(authPath + "/login", {
                username: authData.username,
                password: authData.password
            })
            .then(res => {
                // const now = new Date();
                // const expirationDate = new Date(
                //   now.getTime() + res.data.expiresIn * 1000
                // );
                commit("setLogin", {
                    token: res.data.data.token,
                    user: res.data.data.user,
                    role: res.data.data.role,
                    tenant: res.data.data.tenant,
                    role_code: res.data.data.role_code
                });
                
                //set token di LocalApi
                globals().LocalApi.defaults.headers.common['Authorization'] = 'Bearer ' + state.token; 
                globals().LocalApi.defaults.headers.common['Syn-Api-Token'] = state.token; 

                EventBus.$emit('onLogin',JSON.parse(JSON.stringify(res.data.data)));
                
                //jika auto detek login
                if(
                    globals().AppConfig.system.multitenant.autodetect_login == 1 &&
                    res.data.data.tenant &&
                    res.data.data.tenant.group_app != authData.group_app){ 

                    //load ulang tenant nya
                    return globals().Web.loadTenant(res.data.data.tenant.group_app).then((val)=>{
                        //jika tenant tidak ditemukan
                        if(!val){                    
                            //jika tenant yang tidak ditemukan adalah default tenant maka error
                            if(to.params.group_app == globals().Web.getDefaultTenantRoute().params.group_app){
                                alert('Tenant Api Error');                    
                            }else{                        
                                globals().Web.goToDefaultTenant();
                            }

                        //jika tenant ada maka redirect ke tenant
                        }else{
                            commit("setGroupApp",res.data.data.tenant.group_app);
                            dispatch("implementAcl");
                            EventBus.$emit('onTenantChange',res.data.data.tenant);
                            globals().Web.goToTenant(res.data.data.tenant.group_app);
                        }

                    });                    
                    
                }

                commit("setGroupApp",authData.group_app);
                EventBus.$emit('onTenantChange',authData);
                return dispatch("implementAcl");
            });
    },
    register({ commit, dispatch, state }, authData) {
        var newAuthData = {};

        if(authData.name)
            newAuthData.name = authData.name;

        if(authData.username)
            newAuthData.username = authData.username;

        if(authData.email)
            newAuthData.email = authData.email;

        if(authData.phone)
            newAuthData.phone = authData.phone;

        if(authData.password)
            newAuthData.password = authData.password;

        if(authData.role_code)
            newAuthData.role_code = authData.role_code;

        if(authData.tos_confirm)
            newAuthData.tos_confirm = authData.tos_confirm;
            
        return globals().LocalApi
            .post(authPath + "/register", newAuthData)
            .then(res => {
                EventBus.$emit('onRegister',JSON.parse(JSON.stringify(res.data)));
                
                return JSON.parse(JSON.stringify(res.data));
            });
    },
    forgotPassword({ commit, dispatch, state }, email) {
        return globals().LocalApi
            .post(authPath + "/forgotpassword", email)
            .then(res => {
                EventBus.$emit('onForgotPassword',JSON.parse(JSON.stringify(res.data)));
                
                return JSON.parse(JSON.stringify(res.data));
            });
    },
    changeRole({ commit, state, dispatch },roleCode) {
        return globals().LocalApi
            .get(authPath + "/change_role/" + roleCode).then(res => {
                commit("setActiveRoleCode",roleCode);
                EventBus.$emit('onChangeRole',roleCode);
                return dispatch("implementAcl");
            });
    },
    logout({ commit, state }) {
        commit("setLogout");

        //delete autorization nya
        delete globals().LocalApi.defaults.headers.common['Authorization'];
        delete globals().LocalApi.defaults.headers.common['Syn-Api-Token'];

        let userData = {
            token: state.token,
            user: state.user,
            role: state.role,
            tenant: state.tenant,
            role_code: state.role_code
        }
        EventBus.$emit('onLogout',JSON.parse(JSON.stringify(userData)));
    },
    //---------------------------------------
    initAuth({ commit, state, dispatch }) {},
    /*
    implement acl user yang online sekarang ke sidebar, baru bisa 3 level
    un-elegan way, nanti ubah agar lebih efisien
    */
    implementAcl({ commit, state, dispatch }) {
        var aclItem, aclItemLv2, aclItemLv3, curAclId;

        globals().AppConfig.sidenav = JSON.parse(JSON.stringify(globals().AppConfig.sidenavOri));
        
        _.forEach(globals().AppConfig.sidenav, (vPackage, packageNamespace) => {

            //jika tidak punya akses acl maka tolak (menu akan ditampilkan sesuai default ACL nya)
            if( !state.role[state.role_code] 
                || !state.role[state.role_code]['rule']
                || state.role[state.role_code]['rule'][packageNamespace]==undefined)return true;

            vPackage.has_access = state.role[state.role_code]['rule'][packageNamespace]['has_access']==1?1:0;

            //jika tidak punya specific rule maka tolak (menu akan ditampilkan sesuai default ACL nya)
            if(vPackage.children==undefined)return true;
            
            //----cek hak akses level 1
            _.forEach(vPackage.children, (accessItem, aclId) => {
                curAclId = packageNamespace + '.' + aclId;
                
                //jika tidak punya akses acl maka tolak (menu akan ditampilkan sesuai default active_acl di packageConfig nya)
                if(state.role[state.role_code]['rule'][curAclId]==undefined)return true;
                
                aclItem = state.role[state.role_code]['rule'][curAclId];
                
                if (aclItem.has_access) {
                    accessItem.active_acl = {
                        has_access: aclItem.has_access,
                        crud: { c: aclItem.c, r: aclItem.r, u: aclItem.u, d: aclItem.d }
                    };
                } else {
                    accessItem.active_acl = {
                        has_access: 0,
                        crud: { c: 0, r: 0, u: 0, d: 0 }
                    };
                }
                
                //----cek hak askses level 2 jika ada
                if (accessItem.children != undefined) {
                _.forEach(accessItem.children, (accessItemLv2, aclIdLv2) => {

                    curAclId = packageNamespace + '.' + aclId + '.' +aclIdLv2;

                    if(state.role[state.role_code]['rule'][curAclId]==undefined)return true;

                    aclItemLv2 = state.role[state.role_code]['rule'][curAclId];

                    if (aclItemLv2.has_access) {
                        accessItemLv2.active_acl = {
                            has_access: aclItemLv2.has_access,
                            crud: {
                                c: aclItemLv2.c,
                                r: aclItemLv2.r,
                                u: aclItemLv2.u,
                                d: aclItemLv2.d
                            }
                        };
                    } else {
                        accessItemLv2.active_acl = {
                            has_access: 0,
                            crud: { c: 0, r: 0, u: 0, d: 0 }
                        };
                    }

                    //----cek hak askses level 3 jika ada
                    if (accessItemLv2.children != undefined) {
                        _.forEach(accessItemLv2.children, (accessItemLv3, aclIdLv3) => {

                            curAclId = packageNamespace + '.' + aclId + '.' + aclIdLv2 + '.' + aclIdLv3;

                            if(state.role[state.role_code]['rule'][curAclId]==undefined)return true;

                            aclItemLv3 = state.role[state.role_code]['rule'][curAclId];

                            if (aclItemLv3.has_access) {
                                accessItemLv3.active_acl = {
                                    has_access: aclItemLv3.has_access,
                                    crud: {
                                    c: aclItemLv3.c,
                                    r: aclItemLv3.r,
                                    u: aclItemLv3.u,
                                    d: aclItemLv3.d
                                    }
                                };
                            } else {
                                accessItemLv3.active_acl = {
                                    has_access: 0,
                                    crud: { c: 0, r: 0, u: 0, d: 0 }
                                };
                            }
                        });
                    }
                });
                }
            });
        });
        commit("setSidenavMenu");
    }
};

//format role
var role = {
    role_code_1: {
        is_main_role:1,
        has_auth_grant:1,
        rule: {
            moduleNameSpace: {
                has_access: 1, //apakah punya akses secara keseluruhan terhadap module ini       
            },
            acl_key_2: {
                has_access: 1, //apakah punya akses secara keseluruhan terhadap fitur ini
                c: 1, //apakah punya akses create di fitur ini
                r: 1, //apakah punya akses read di fitur ini
                u: 1, //apakah punya akses update di fitur ini
                d: 1 //apakah punya akses delete di fitur ini
            }
        }
    }
};

export default {
    state,
    mutations,
    actions,
    getters
};
