import {createStore} from "vuex";

const store = createStore({
    state:{
        testValue:"This is a test, if it appeared you have tested it.",
        token:null,
        user:null,
    },
    mutations:{
        printValue(state){
            console.log(state.testValue);
        }
    },
    actions:{
        printAction(context){
            context.commit('printValue');
        }
    },
    getters:{
        testValues(state){
            return state.testValue;
        },
        getToken(state){
            return state.token;
        }
    }
})

export default store;
