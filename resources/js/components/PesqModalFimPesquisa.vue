<template>
    <div class="modal" tabindex="-1" role="dialog" id="mdlFimPesquisa">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Envio de repostas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Confirma o envio das suas respostas para esta disciplina?
                    </p>
                    <p>
                        Uma vez enviados os dados, os mesmos não poderão ser alterados, pois não guardamos a
                        associação entre você e as suas respostas.
                    </p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" @click="$emit('envia',false)" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" @click="$emit('envia',true)" data-dismiss="modal">Enviar respostas</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {}
    },

    methods: {}
}
</script>
