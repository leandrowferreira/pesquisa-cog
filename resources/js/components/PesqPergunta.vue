<template>
    <div class="carousel-item" :class="{active : numero == 1}">

        <div class="card">
            <div class="card-header" :class="{'text-white bg-success' : resposta !== null}">
                <strong>Pergunta {{numero}} de {{total}}</strong>
                <font-awesome-icon v-show="resposta !== null" class="ml-1" icon="check-circle"></font-awesome-icon>
            </div>
            <div class="card-body">

                <div v-if="erro(numero)" class="alert alert-warning alert-dismissible fade show">
                    {{ tipo != 4 ? 'Você precisa responder esta pergunta!' : 'Preencha os seus dados ou desmarque a opção de feedback' }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="row">
                    <div class="col-12" style="height:5em;">
                        <h4 class="d-none d-md-block">{{texto}}</h4>
                        <strong class="d-block d-md-none">{{texto}}</strong>
                    </div>
                </div>

                <div class="row">
                    <div v-if="tipo == 1 || tipo == 2" class="col-md-3">
                        <a href="#" class="mt-4 btn btn-block" :class="{'btn-outline-dark': resposta != 1, 'btn-success' : resposta == 1}" @click.prevent="resposta = 1">Sim</a>
                    </div>
                    <div v-if="tipo == 2 || tipo == 1" class="col-md-3">
                        <a href="#" class="mt-4 btn btn-block" :class="{'disabled': tipo == 1, 'btn-outline-dark': resposta != 2, 'btn-success' : resposta == 2}" @click.prevent="resposta = 2" >Às vezes</a>
                    </div>
                    <div v-if="tipo == 1 || tipo == 2" class="col-md-3">
                        <a href="#" class="mt-4 btn btn-block" :class="{'btn-outline-dark': resposta != 3, 'btn-success' : resposta == 3}" @click.prevent="resposta = 3">Não</a>
                    </div>
                    <div v-if="tipo == 1 || tipo == 2" class="col-md-3">
                        <a href="#" class="mt-4 btn btn-block" :class="{'btn-outline-dark': resposta != 4, 'btn-success' : resposta == 4}" @click.prevent="resposta = 4">Não sei responder</a>
                    </div>

                    <div v-if="tipo == 4" class="col-12">
                        <textarea class=" mt-2 form-control" rows="6" v-model="resposta"></textarea>
                        <small class="form-text text-muted">Esta pergunta é opcional</small>
                    </div>

                    <div v-if="tipo == 3 && resposta1" class="col-md-4">
                        <a href="#" class="mt-4 btn btn-block" :class="{'btn-outline-dark': resposta != 1, 'btn-success' : resposta == 1}" @click.prevent="resposta = 1">{{resposta1}}</a>
                    </div>

                    <div v-if="tipo == 3 && resposta2" class="col-md-4">
                        <a href="#" class="mt-4 btn btn-block" :class="{'btn-outline-dark': resposta != 2, 'btn-success' : resposta == 2}" @click.prevent="resposta = 2">{{resposta2}}</a>
                    </div>

                    <div v-if="tipo == 3 && resposta3" class="col-md-4">
                        <a href="#" class="mt-4 btn btn-block" :class="{'btn-outline-dark': resposta != 3, 'btn-success' : resposta == 3}" @click.prevent="resposta = 3">{{resposta3}}</a>
                    </div>

                </div>

                <div class="row" v-if="temFeedback">
                    <div class="col-12">
                        <form>
                            <div class="form-group form-check mt-4">
                                <input type="checkbox" v-model="feedback" class="form-check-input" :id="'chkFeedback' + numero">
                                <label class="form-check-label" :for="'chkFeedback' + numero">Desejo receber <i>feedback</i> referente a minha resposta a esta pergunta</label>
                            </div>

                            <div v-if="feedback" class="form-group">
                                <label :for="'edtNome' + numero">Seu nome</label>
                                <input type="text" v-model="nome" class="form-control" :id="'edtNome' + numero" placeholder="Seu nome">
                            </div>

                            <div v-if="feedback" class="form-group">
                                <label :for="'edtEmail' + numero">Seu e-mail</label>
                                <input type="email" v-model="email" class="form-control" :id="'edtEmail' + numero" placeholder="Seu endereço de e-mail">
                                <small :id="'emailHelp' + numero" class="form-text text-muted">Nós não compartilhamos seus dados com ninguém, nem mesmo com o professor da disciplina!</small>
                            </div>

                        </form>
                    </div>

                </div>




            </div>
            <div class="card-footer">
                <a v-if="numero > 1" href="#carouselPerguntas" class="btn btn-primary" role="button" data-slide="prev">
                    <font-awesome-icon class="mr-1" icon="arrow-circle-left"></font-awesome-icon>
                    Anterior
                </a>

                <a v-if="numero < total" href="#carouselPerguntas" class="btn btn-primary float-right" role="button" data-slide="next">
                    Próxima
                    <font-awesome-icon class="ml-1" icon="arrow-circle-right"></font-awesome-icon>
                </a>

                <a v-if="numero >= total" href="#carouselPerguntas" class="btn btn-success float-right" @click="finaliza" data-slide="next">
                    Minhas respostas
                    <font-awesome-icon class="ml-1" icon="arrow-circle-right"></font-awesome-icon>
                </a>

            </div>
        </div>
    </div>

</template>

<script>

import {globalVar} from '../app.js'

export default {
    props: ['total','numero','tipo','temFeedback', 'resposta1','resposta2','resposta3'],
    data: function() {
        return {
            texto: '',
            resposta: null,
            feedback: false,
            nome: '',
            email: ''
        }
    },

    mounted: function() {
        this.texto = this.$slots.default[0].text
    },

    methods: {
        finaliza:function() {
            this.$emit('finaliza',globalVar.getRespostas())
        },

        erro: function(num) {
            return globalVar.getErro(num)
        }
    },

    watch: {
        resposta: function(val) {
            if (this.tipo != 4) {
                globalVar.setResposta(this.numero, val)
            } else {
                globalVar.setRespostaDiscursiva(this.numero, this.resposta, this.feedback, this.nome, this.email)
            }
            this.$emit('responde',val)
        },

        feedback: function() {
            globalVar.setRespostaDiscursiva(this.numero, this.resposta, this.feedback, this.nome, this.email)
            this.nome = ''
            this.email = ''
        },

        nome: function() {
            globalVar.setRespostaDiscursiva(this.numero, this.resposta, this.feedback, this.nome, this.email)
        },

        email: function() {
            globalVar.setRespostaDiscursiva(this.numero, this.resposta, this.feedback, this.nome, this.email)
        }
    }
}
</script>
