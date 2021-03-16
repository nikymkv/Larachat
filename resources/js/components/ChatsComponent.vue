<template>
    <div class="container">
        <hr>
        <div class="raw">
            <div class="col-sm-12">
                <ul>
                    <li v-for="value in chats" v-bind:key="value.id">
                        <a :href="'/chats/' + value.id">
                            <div>
                                Название: {{ value.chat_title }}<br>
                                Последее сообщение: {{ value.last_message }}
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                chats: [],
            }
        },
        mounted() {
        },
        computed: {
            channel() {
                return window.Echo.private('user.' + this.user.id)
            }
        },
        created() {
            this.fetchChats()
            this.channel
                .listen('NewMessage', (event) => {
                    let data = event.data
                    this.chats = this.chats.filter(c => c.id != data.chat_id)
                    this.chats.splice(0, 0, {
                        id: data.chat_id,
                        chat_title: data.name,
                        last_message: data.content,
                    })
                })
                // .notification((notification) => {
                //     console.log(notification.data);
                // });
        },
        methods: {
            fetchChats() {
                axios.get('chats/fetch-all').then(response => {
                    this.chats = JSON.parse(response.data);
                    console.log(JSON.parse(response.data))
                })
            },
        }
    }
</script>
