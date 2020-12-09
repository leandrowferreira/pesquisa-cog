<template>
    <div class="modal" tabindex="-1" role="dialog" id="mdlPrivacidade">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aviso de privacidade</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Nós garantimos seu anonimato.</strong> As suas respostas, quando armazenadas no banco de
                    dados, não serão associadas a você nem serão passíveis de associação. Pedimos para que você se
                    autentique apenas para saber sobre quais professores você pode opinar.
                </p>

                <p>
                    O código-fonte deste programa está disponível publicamente para ser auditado por qualquer
                    pessoa que se interessar por este assunto.
                </p>
                <hr>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="chkPrivacidade" v-model="naoAvisar" @change="muda">
                    <label class="form-check-label" for="chkPrivacidade">
                        Estou ciente. Não me avise novamente.
                    </label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['privacidade', 'id'],
    data: function() {
        return {
            naoAvisar: false,
        }
    },

    mounted: function() {
        this.naoAvisar = !this.privacidade
    },

    methods: {
        muda: function() {
            axios
                .patch('/async/user/' + this.id, {privacidade: !this.naoAvisar})
                .then(function(response) {})
        }
    }
}
</script>
