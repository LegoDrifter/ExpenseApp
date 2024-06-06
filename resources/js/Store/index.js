import {createStore} from "vuex";

const store = createStore({
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
        },
        logAuth(state, payload){
            state.token = payload.token;
            state.user = payload.user;
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
        isAuthenticated:state => !!state.token,


    }
})

export default store;
