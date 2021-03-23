<template>
   <div class="row">

       <div class="col-8">
           <div class="card card-default">
               <div class="card-header">Messages</div>
               <div class="card-body p-0">
                   <ul class="list-unstyled" style="height:300px; overflow-y:scroll" v-chat-scroll>
                       <li class="p-2" v-for="(message, index) in messages" :key="index" >
                           <strong>{{ message.name }}[{{toDateTime(message.created_at)}}]</strong>
                           {{ message.content }}
                       </li>
                   </ul>
               </div>

               <input
                    @keydown="sendTypingEvent"
                    @keyup.enter="sendMessage"
                    v-model="newMessage"
                    type="text"
                    name="message"
                    placeholder="Enter your message..."
                    class="form-control">
           </div>
            <span class="text-muted" v-if="activeUser" >{{ activeUser.name }} is typing...</span>
       </div>

        <div class="col-4">
            <div class="card card-default">
                <div class="card-header">Members</div>
                <div class="card-body">
                    <ul>
                        <li class="py-2" v-for="(user, index) in users" :key="index">
                            {{ user.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

   </div>
</template>

<script>
    export default {
        props: {
            user: {
                type: Object
            },
            chat_id: {
                type: Number
            }
        },
        data() {
            return {
                messages: [],
                newMessage: '',
                users: [],
                activeUser: false,
                typingTimer: false,
                participants: [],
            }
        },
        created() {
            this.fetchMessages()
            Echo.join('chat.' + this.chat_id)
                .here(user => {
                    this.users = user;
                })
                .joining(user => {
                    this.users.push(user);
                })
                .leaving(user => {
                    this.users = this.users.filter(u => u.id != user.id);
                })
                .listen('PrivateChat',(event) => {
                    console.log(event.data)
                    this.messages.push(event.data);
                })
                .listenForWhisper('typing', user => {
                   this.activeUser = user;
                    if(this.typingTimer) {
                        clearTimeout(this.typingTimer);
                    }
                   this.typingTimer = setTimeout(() => {
                       this.activeUser = false;
                   }, 1000);
                })
        },
        methods: {
            fetchMessages() {
                axios.get(this.chat_id + '/messages').then(response => {
                    let data = JSON.parse(response.data);
                    this.messages = data.messages;
                    this.participant = data.participant;
                })
            },
            sendMessage() {
                let curTime = Date.now()

                this.messages.push({
                    name: this.user.name,
                    content: this.newMessage,
                    created_at: curTime,
                });
                axios.post('messages', {
                    user_id: this.user.id,
                    name: this.user.name,
                    chat_id: this.chat_id,
                    content: this.newMessage,
                    created_at: curTime,
                    participant: this.participant,
                });
                this.newMessage = '';
                console.log(this.messages)
            },
            sendTypingEvent() {
                Echo.join('chat.' + this.chat_id)
                    .whisper('typing', this.user);
            },

            toDateTime(dateTime) {
                console.log(dateTime)
                let date = new Date(dateTime);
                return ("0" + date.getHours()).slice(-2)+ ':' +
                    ("0" + date.getMinutes()).slice(-2) + ' ' +
                    ("0" + date.getDate()).slice(-2) + '-' +
                    ("0" + (date.getMonth() + 1)).slice(-2) + '-' +
                    date.getFullYear()
            }
        }
    }
</script>