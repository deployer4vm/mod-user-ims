import authStore from "./auth";

const state = {
    example_data: 'userdata'
};

const getters = {};

const mutations = {
    changeData (state, data) {
      state.example_data = data
    }
};

const actions = {
    updateData({commit}, data) {
        commit('changeData', data)
    }
};

const userStore = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

export default {userStore,auth: authStore};