import globals from "@/globals";
var userApi = globals().AppConfig.endpoint.api.moduser;

const state = {
    roleList: {
        data: [],
        count: 0,
        limit: 10,
        offset: 0,
        currentPage: 1,
        pageCount: 1
    },
    roleForm: {}
};

const getters = {
    getRoleList(state) {
        return state.userList;
    },
    getRole(state) {
        return state.roleForm;
    }
};

const mutations = {
    setRoleList(state, data) {
        state.roleList = data;
    },
    setRole(state, data) {
        state.roleForm = data;
    }
};

const actions = {
    roleList({ commit, dispatch, state }, filterParams) {
        return globals()
            .LocalApi.get(userApi + "/role" , filterParams)
            .then(res => {
                commit("setRoleList", res.data.data);
                return res.data.data;
            });
    },
    getRole({ commit }, id) {
        return globals()
            .LocalApi.get(masterApi + "/role/" + id)
            .then(res => {
                commit("setRole", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
    },
    create({commit},data) {

        return globals()
            .LocalApi.post(masterApi + "/role")
            .then(res => {
                commit("setRole", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
    },
    update({commit},data){

    },
    delete({commit},id){

    }
};

const role = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};

export default role;
