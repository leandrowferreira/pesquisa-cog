<template>
  <div id="carouselPerguntas" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
      <pesq-pergunta
        v-for="pergunta in perguntas"
        :key="pergunta.id"
        :total="perguntas.length"
        :numero="pergunta.numero"
        :tipo="pergunta.tipo"
        :resposta1="pergunta.resposta1"
        :resposta2="pergunta.resposta2"
        :resposta3="pergunta.resposta3"
        :temFeedback="pergunta.feedback"
        @responde="mudaResposta(pergunta.numero, $event)"
        @finaliza="atualiza($event)"
      >{{pergunta.texto}}</pesq-pergunta>

      <div class="carousel-item">
        <div class="card">
          <div class="card-header">
            <strong>Resumo</strong>
          </div>
          <div class="card-body">
            <div
              v-for="pergunta in perguntas"
              :key="pergunta.numero"
              class="alert"
              :class="{'alert-success': respostas[pergunta.numero], 'alert-danger': !respostas[pergunta.numero]}"
            >
              <p>
                <strong>{{pergunta.numero}}.</strong>
                {{pergunta.texto}}
              </p>
              <p>
                Resposta:
                <span
                  v-if="pergunta.tipo < 3"
                  class="badge p-1"
                  :class="'badge-' + resposta(pergunta.tipo, respostas[pergunta.numero],true)"
                >{{resposta(pergunta.tipo, respostas[pergunta.numero], false)}}</span>
                <span
                  v-if="pergunta.tipo == 3"
                >{{pergunta['resposta' + respostas[pergunta.numero]]}}</span>
                <span
                  v-if="pergunta.tipo == 4"
                >{{respostas[pergunta.numero] ? respostas[pergunta.numero].resposta : '-'}}</span>
              </p>

              <hr>

              <a
                class="btn btn-sm"
                :class="respostas[pergunta.numero] ? 'btn-success' : 'btn-danger'"
                href="#carouselPerguntas"
                :data-slide-to="pergunta.numero-1"
              >
                <font-awesome-icon class="mr-1" icon="arrow-circle-left"></font-awesome-icon>Voltar à pergunta
              </a>
            </div>
          </div>
          <div class="card-footer">
            <a href="#carouselPerguntas" class="btn btn-primary" role="button" data-slide="prev">
              <font-awesome-icon class="mr-1" icon="arrow-circle-left"></font-awesome-icon>Anterior
            </a>

            <a
              href="#"
              class="btn btn-success float-right"
              :disabled="enviado"
              @click.prevent="envia"
            >Finalizar
              <font-awesome-icon class="ml-1" icon="check-circle"></font-awesome-icon>
            </a>
          </div>
        </div>
      </div>
    </div>

    <pesq-modal-privacidade :privacidade="privacidade" :id="id"></pesq-modal-privacidade>
    <pesq-modal-fim-pesquisa v-on:envia="conclui($event)"></pesq-modal-fim-pesquisa>
  </div>
</template>

<script>
import { globalVar } from "../app.js";

export default {
  props: ["discId", "profId"],

  data: function() {
    return {
      privacidade: true,
      enviado: false,
      id: null,
      perguntas: [],
      respostas: {}
    };
  },

  methods: {
    mudaResposta: function(num, resp) {
      // globalVar.$data.respostas[num] = resp
      this.respostas = globalVar.getRespostas();
      globalVar.clearErro(num);
      this.$forceUpdate();
    },

    atualiza: function(respostas) {
      this.respostas = globalVar.getRespostas();
      this.$forceUpdate();
    },

    envia: function() {
      //Verifica se todas as questões estão respondidas
      let erro = 0;
      for (let i in this.perguntas) {
        let num = this.perguntas[i].numero;

        switch (this.perguntas[i].tipo) {
          case 1:
          case 2:
          case 3:
            if (!this.respostas[num]) {
              globalVar.setErro(num);
              this.$forceUpdate();
              if (!erro) {
                erro = num;
              }
            }
            break;
          case 4:
            if (this.respostas[num].feedback && this.respostas[num].resposta) {
              if (!this.respostas[num].nome || !this.respostas[num].email) {
                globalVar.setErro(num);
                this.$forceUpdate();
                if (!erro) {
                  erro = num;
                }
              }
            }
            break;
        }
      }
      if (erro) {
        $(".carousel").carousel(erro - 1);
        return false;
      }

      //Apresenta a modal, responsável por concluir o post
      $("#mdlFimPesquisa").modal();
    },

    conclui: function(envia) {
      this.enviado = true;
      if (envia) {
        axios
          .post("/async/disciplinas/" + this.discId + '/' + this.profId, this.respostas)
          .then(function(response) {
            if (response.status == 200) {
              window.location = "/disciplinas";
            }
          });
      }
    },

    resposta: function(tipo, resp, badge = false) {
      switch (tipo) {
        case 1:
        case 2:
          switch (resp) {
            case 1:
              return badge ? "success" : "Sim";
            case 2:
              return badge ? "warning" : "Às vezes";
            case 3:
              return badge ? "danger" : "Não";
            case 4:
              return badge ? "secondary" : "Não sei responder";
            default:
              return badge ? "primary" : "Sem resposta";
          }
          break;
      }
    }
  },

  mounted: function() {
    let self = this;

    this.enviado = false;

    axios.get("/async/perguntas").then(function(response) {
      self.perguntas = response.data;
      for (let i in self.perguntas) {
        if (self.perguntas[i].tipo == 4) {
          globalVar.setRespostaDiscursiva(
            self.perguntas[i].numero,
            null,
            false,
            null,
            null
          );
        } else {
          globalVar.setResposta(self.perguntas[i].numero, null);
        }
      }
    });

    axios.get("/async/user").then(function(response) {
      if (response.data.aviso_privacidade) {
        self.privacidade = response.data.aviso_privacidade;
        self.id = response.data.id;

        $("#mdlPrivacidade").modal();
      }
    });
  }
};
</script>
