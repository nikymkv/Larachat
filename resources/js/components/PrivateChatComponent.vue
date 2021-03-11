<template>
    <div class="container">
        <hr>
        <div class="row">
            <div class="col-sm-8">
                <textarea class="form-control" name="" id="" cols="30" rows="15" readonly="">{{ messages.join('\n') }}</textarea>
                <hr>
                <input type="text" class="form-control" v-model="textMessage" @keyup.enter="sendMessage" @keydown="actionUser">
                <span v-if="isActive">{{ isActive.name }} набирает сообщение...</span>
            </div>
            <div class="col-sm-4">
                <ul>
                    <li v-for="user in activeUsers" >{{ user }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['room', 'user'],
        data() {
            return {
                messages: [],
                textMessage: '',
                isActive: false,
                typingTimer: false,
                activeUsers: [],
            }
        },
        computed: {
            channel() {
                return window.Echo.join('room.' + this.room.id)
            }
        },
        mounted() {
            this.channel
                .here((users) => {
                    this.activeUsers = users
                })
                .joining((user) => {
                    this.activeUsers.push(user)
                })
                .leaving((user) => {
                    this.activeUsers.splice(this.activeUsers.indexOf(user), 1)
                })
                .listen('PrivateChat', ({data}) => {
                    this.messages.push(data.name + ': ' + data.msg)
                    this.isActive = false
                })
                .listenForWhisper('typing', (e) => {
                    this.isActive = e
                    if (this.typingTimer) {
                        clearTimeout(this.typingTimer)
                    }
                    this.typingTimer = setTimeout(() => {
                       this.isActive = false 
                    }, 2000);
                })
        },
        methods: {
            sendMessage() {
                axios.post('/private-message', {name: this.user.name, msg: this.textMessage, room_id: this.room.id })
                this.messages.push(this.user.name + ': ' + this.textMessage)
                this.textMessage = ''
            },
            actionUser() {
                this.channel
                    .whisper('typing', {
                        name: this.user.name
                    })
            }
        }
    }
</script>
