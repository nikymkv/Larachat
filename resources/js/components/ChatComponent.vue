<template>
    <div class="container">
        <hr>
        <div class="raw">
            <div class="col-sm-8">
                <textarea class="form-control" name="" id="" cols="30" rows="15" readonly="">{{ messages.join('\n') }}</textarea>
                <hr>
                <input type="text" class="form-control" v-model="textMessage" @keyup.enter="sendMessage">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                messages: [],
                textMessage: ''
            }
        },
        mounted() {
            window.Echo.channel('chat').listen('PublicChat', ({message}) => {
                this.messages.push(message)
                console.log(message)
            } )
        },
        methods: {
            sendMessage() {
                axios.post('/messages', { body: this.textMessage })
                this.messages.push(this.textMessage)
                this.textMessage = ''
            }
        }
    }
</script>
