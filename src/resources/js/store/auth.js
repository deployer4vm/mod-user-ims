const state = {
    idToken: null, //token akses API
    userId: null,
    user: null, //data komplit user
    acl: null //list access control user
};

const getters = {
    user (state) {
      return state.user
    },
    isLogin(state) {
        return state.idToken !== null;
    }
};

const mutations = {
    /*
    params 
        userData :
            token
            user
            acl
    */
    setLogin(state, userData) {
        state.idToken = userData.token;
        state.user = userData.user;
        state.userId = userData.user.id;
        state.acl = userData.acl;
    },
    setLogout(state){        
        state.idToken = null;
        state.userId = null;
        state.user = null;
        state.acl = null;
    }
};

const actions = {
    setLogin({ commit }, userData) {
        commit("setLogin", userData);
    }
};

export default {
    state,
    mutations,
    actions,
    getters
};
