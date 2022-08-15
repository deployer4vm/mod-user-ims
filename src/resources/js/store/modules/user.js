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
    setUserList(state, data) {
        // _.forEach(data.data,(v,i)=>{
        //     v.roles = v.role.split(';');
        // });
        state.userList = data;
    },
    setUser(state, data) {
        state.user = data;
    }
};

const actions = {
    userList({ commit, dispatch, state }, params) {
        return globals()
            .LocalApi.get(userApi , {
                params: params.params
            })
            .then(res => {
                if(params.saveState==undefined||params.saveState)
                    commit("setUserList", res.data.data);
                return res.data.data;
            });
    },
    getUser({ commit }, id) {
        return globals()
            .LocalApi.get(userApi + "/" + id)
            .then(res => {
                commit("setUser", res.data.data);
                return res.data.data;
            });
    },
    updateProfile({commit},data){
        return globals()
            .LocalApi.put(userApi + "/profile",data)
            .then(res => {
                commit("setUser", res.data.data);
                return true;
            });
        
    },
    updatePassword({commit},data){
        return globals()
            .LocalApi.put(userApi + "/" + data.id + "/updatepassword",data)
            .then(res => {
                commit("setUser", res.data.data);
                return true;
            });
        
    },
    register({commit},data) {
        var formData = globals().Helper.convertToFormData(data);
        return globals()
            .LocalApi.post(userApi,formData,{
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(res => {
                commit("setUser", res.data.data);
                return true;
            });
    },
    update({commit},data){
        var formData = globals().Helper.convertToFormData(data.data);
        return globals()
            .LocalApi.post(userApi + "/" + data.id,formData,{
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(res => {
                commit("setUser", res.data.data);
                return true;
            });
        
    },
    delete({commit},id){
        return globals()
            .LocalApi.delete(userApi + "/" + id)
            .then(res => {
                commit("setUser", res.data.data);
                return true;
            });
    },    
    resentVerificationMail({commit},id){
        return globals()
            .LocalApi.put(userApi + "/" + id + "/resent-verification-mail")
            .then(res => {
                // commit("setUser", res.data.data);
                return res.data.data;
            });
        
    },
};

const user = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};

export default user;
