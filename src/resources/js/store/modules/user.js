import globals from "@/globals";
var userApi = globals().AppConfig.endpoint.api.moduser;

const state = {
    userList: {
        data: [],
        count: 0,
        limit: 10,
        offset: 0,
        currentPage: 1,
        pageCount: 1
    },
    userForm: {},//for edit/view user
    user:{}//single user for other purpose
};

const getters = {
    getUserList(state) {
        return state.userList;
    },
    getUserForm(state) {
        return state.userForm;
    }
};

const mutations = {
    setListUser(state, data) {
        state.userList = data;
    },
    setUserForm(state, data) {
        state.userForm = data;
    }
};

const actions = {
    userList({ commit, dispatch, state }, filterParams) {
        return globals()
            .LocalApi.get(userApi , filterParams)
            .then(res => {
                commit("setListUser", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
    },
    getUser({ commit }, id) {
        return globals()
            .LocalApi.get(userApi + "/" + id)
            .then(res => {
                commit("setUserForm", res.data.data);
                return res.data.data;
            });
    },
    updateProfile({commit},data){
        return globals()
            .LocalApi.put(userApi + "/profile",data)
            .then(res => {
                commit("setUser", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
        
    },
    updatePassword({commit},data){
        return globals()
            .LocalApi.put(userApi + "/" + data.id + "/updatepassword",data)
            .then(res => {
                commit("setUser", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
        
    },
    register({commit},data) {
        return globals()
            .LocalApi.post(userApi,data)
            .then(res => {
                commit("setUser", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
    },
    update({commit},data){
        return globals()
            .LocalApi.post(userApi,data)
            .then(res => {
                commit("setUser", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
        
    },
    delete({commit},id){
        return globals()
            .LocalApi.post(userApi + "/" + id)
            .then(res => {
                commit("setUser", res.data.data);
                return new Promise((resolve,err)=>{
                    resolve(true);
                });
            });
        
    }
};

const user = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};

export default { user };
