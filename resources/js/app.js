
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import { library } from '@fortawesome/fontawesome-svg-core'
import { faCoffee, faAd, faSkullCrossbones, faArrowCircleLeft, faArrowCircleRight, faCheckCircle } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import VueTheMask from 'vue-the-mask'
Vue.use(VueTheMask)

//Ícones FA usados
library.add([faCoffee, faAd, faSkullCrossbones, faArrowCircleLeft, faArrowCircleRight, faCheckCircle])


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.component('pesq-modal-privacidade', require('./components/PesqModalPrivacidade.vue').default);
Vue.component('pesq-modal-fim-pesquisa', require('./components/PesqModalFimPesquisa.vue').default);

Vue.component('pesq-perguntas', require('./components/PesqPerguntas.vue').default);
Vue.component('pesq-pergunta', require('./components/PesqPergunta.vue').default);



export const globalVar = new Vue({
    data: {
        respostas: {},
        erros: {}
    },

    methods: {
        setResposta: function(num,resp) {
            this.respostas[num] = resp
        },

        setRespostaDiscursiva: function(num, resposta, feedback, nome, email) {
            this.respostas[num] = {
                resposta: resposta,
                feedback: feedback,
                nome: nome,
                email: email,
            }
        },

        getResposta: function(num) {
            return this.respostas[num]
        },

        getRespostas: function() {
            return this.respostas
        },

        getErro: function(num) {
            return this.erros[num]
        },

        setErro: function(num) {
            this.erros[num] = true
        },

        clearErro: function(num) {
            this.erros[num] = false
        },

    }
})



const app = new Vue({
    el: '#app',
    mounted: function() {
        $('.carousel').carousel({
            interval: false
          })
    }
});
