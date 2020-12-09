<template>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <font-awesome-icon icon="ad" />
                    Example Component
                    </div>

                <div class="card-body">
                    <font-awesome-icon icon="skull-crossbones" />
                    I'm an example component.
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted!')
        }
    }
</script>
