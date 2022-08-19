import globals from "@/globals";
var userApi = globals().AppConfig.endpoint.api.moduser;

const state = {
    userList: {
        data: [],
        count: 0,
        limit: 10,
        offset: 0,
        currentPage: 1,
        pageCount: 1,
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
            .LocalApi.get(userApi + "/usersystem" , {
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
            .LocalApi.get(userApi + "/usersystem/" + id)
            .then(res => {
                commit("setUser", res.data.data);
                return res.data.data;
            });
    },
    register({commit},data) {
        var formData = globals().Helper.convertToFormData(data);
        return globals()
            .LocalApi.post(userApi + "/usersystem", formData,{
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
            .LocalApi.post(userApi + "/usersystem/" + data.id,formData,{
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
            .LocalApi.delete(userApi + "/usersystem/" + id)
            .then(res => {
                commit("setUser", res.data.data);
                return true;
            });
    },
    generateToken({commit},id){
        return globals()
            .LocalApi.put(userApi + "/usersystem/" + id + "/generate_token")
            .then(res => {
                commit("setUser", res.data.data);
                return true;
            });
    }
};

const usersystem = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};

export default usersystem;
