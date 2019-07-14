import globals from "@/globals";

var authPath = globals().AppConfig.endpoint.api.auth;

const state = {
  token: null, //token akses API
  userId: null,
  user: null, //data komplit user
  role: null, //list access control user
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
    state.role_code = userData.role_code;
  },
  setLogout(state) {
    state.token = null;
    state.userId = null;
    state.user = null;
    state.role = null;
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
            role_code: res.data.data.role_code
        });
        commit("setGroupApp",authData.group_app);
        //set token di LocalApi
        globals().LocalApi.defaults.headers.common['Authorization'] = 'Bearer ' + state.token;

        return dispatch("implementAcl");
      });
  },
  logout({ commit }) {
    commit("setLogout");

    //delete autorization nya
    delete globals().LocalApi.defaults.headers.common['Authorization'];
  },
  //---------------------------------------
  initAuth({ commit, state, dispatch }) {},
  /*
  implement acl user yang online sekarang, baru bisa 3 level
  un-elegan way, nanti ubah agar lebih efisien
  */
  implementAcl({ commit, state, dispatch }) {
    var aclItem, aclItemLv2, aclItemLv3;
    
    _.forEach(globals().AppConfig.sidenav, (vPackage, packageNamespace) => {
      //----cek hak akses level 1
      _.forEach(vPackage.children, (accessItem, aclId) => {
        
        //jika tidak punya akses acl maka tolak (menu akan ditampilkan sesuai default ACL nya)
        if( !state.role[state.role_code] 
          || !state.role[state.role_code]['rule']
          || state.role[state.role_code]['rule'][packageNamespace]==undefined 
          || state.role[state.role_code]['rule'][packageNamespace][aclId]==undefined)return;
        
        aclItem = state.role[state.role_code]['rule'][packageNamespace][aclId];
        
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

            if(aclItemLv2.children[aclIdLv2]==undefined)return;

            aclItemLv2 = aclItemLv2.children[aclIdLv2];
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

                if(aclItemLv2.children[aclIdLv3]==undefined)return;

                aclItemLv3 = aclItemLv2.children[aclIdLv3];
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

  }
};

//format role
var role = {
  role_code_1: {
    is_main_role:1,
    has_auth_grant:1,
    rule: {
      moduleNameSpace: {
        acl_key_1: {
          has_access: 1, //apakah punya akses secara keseluruhan terhadap fitur ini
          c: 1, //apakah punya akses create di fitur ini
          r: 1, //apakah punya akses read di fitur ini
          u: 1, //apakah punya akses update di fitur ini
          d: 1, //apakah punya akses delete di fitur ini
          children: {
            // jika ada sub fitur lain
            acl_key_1_1: {
              has_access: 1, //apakah punya akses secara keseluruhan terhadap fitur ini
              c: 1, //apakah punya akses create di fitur ini
              r: 1, //apakah punya akses read di fitur ini
              u: 1, //apakah punya akses update di fitur ini
              d: 1 //apakah punya akses delete di fitur ini
            }
          }
        },
        acl_key_2: {
          has_access: 1, //apakah punya akses secara keseluruhan terhadap fitur ini
          c: 1, //apakah punya akses create di fitur ini
          r: 1, //apakah punya akses read di fitur ini
          u: 1, //apakah punya akses update di fitur ini
          d: 1 //apakah punya akses delete di fitur ini
        }
      },
      moduleNameSpace: {
        acl_key_1: {
          has_access: 1, //apakah punya akses secara keseluruhan terhadap fitur ini
          c: 1, //apakah punya akses create di fitur ini
          r: 1, //apakah punya akses read di fitur ini
          u: 1, //apakah punya akses update di fitur ini
          d: 1 //apakah punya akses delete di fitur ini
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
  }
};

export default {
  state,
  mutations,
  actions,
  getters
};
