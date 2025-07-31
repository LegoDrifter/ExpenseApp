import {createStore} from "vuex";
import createPersistedState from "vuex-persistedstate";

const store = createStore({
    plugins: [createPersistedState()],
    state:{
        testValue:"This is a test, if it appeared you have tested it.",
        token: null,
        user:  null,
    },
    mutations:{
        printValue(state){
            console.log(state.testValue);
        },
        clearAuth(state){
          state.token = null;
          state.user = null;
            delete axios.defaults.headers.common['Authorization'];
        },
        logAuth(state, payload){
            state.token = payload.token;
            state.user = payload.user;
            axios.defaults.headers.common['Authorization'] = `Bearer ${payload.token}`

        }

    },
    actions:{
        printAction(context){
            context.commit('printValue');
        },
        logoutAction(context){
            context.commit('clearAuth');
        },
        setCredentials(context, payload){
            context.commit('logAuth',payload);
        }


    },
    getters:{
        getUser(state){
            return state.user;
        },
        getToken(state){
            return state.token;
        },
        isAuthenticated:state => !!state.token,


    },

})

export default store;
