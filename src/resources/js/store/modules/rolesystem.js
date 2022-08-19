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
        return state.roleList;
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
            .LocalApi.get(userApi + "/rolesystem" , {
                params: filterParams,
            })
            .then(res => {
                commit("setRoleList", res.data.data);
                return res.data.data;
            });
    },
    getRole({ commit }, id) {
        return globals()
            .LocalApi.get(userApi + "/rolesystem/" + id)
            .then(res => {
                commit("setRole", res.data.data);
                return res.data.data;
            });
    },
    create({commit},data) {
        return globals()
            .LocalApi.post(userApi + "/rolesystem",data)
            .then(res => {
                commit("setRole", res.data.data);
                return res.data.data;
            });
    },
    update({commit},data){
        return globals()
            .LocalApi.put(userApi + "/rolesystem/" + data.id,data.data)
            .then(res => {
                commit("setRole", res.data.data);
                return res.data.data;
            });
    },
    delete({commit},id){
        return globals()
            .LocalApi.delete(userApi + "/rolesystem/" + id)
            .then(res => {
                commit("setRole", res.data.data);
                return res.data.data;
            });
    }
};

const rolesystem = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};

export default rolesystem;
