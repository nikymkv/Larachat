<template>
   <div class="row">

       <div class="col-8">
           <div class="card card-default">
               <div class="card-header">Messages</div>
               <div class="card-body p-0">
                   <ul class="list-unstyled" style="height:300px; overflow-y:scroll" v-chat-scroll>
                       <li class="p-2" v-for="(message, index) in messages" :key="index" >
                           <strong>{{ message.name }}[{{message.created_at}}]</strong>
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
                    let unixTimestamp = Date.parse(event.data.created_at)
                    let date = new Date(unixTimestamp)
                    event.data.created_at = date.getHours() + ':' + date.getMinutes()
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
                let cur = this.currentTime();
                this.messages.push({
                    name: this.user.name,
                    content: this.newMessage,
                    created_at: cur,
                });
                axios.post('messages', {
                    user_id: this.user.id,
                    name: this.user.name,
                    chat_id: this.chat_id,
                    content: this.newMessage,
                    created_at: cur,
                    participant: this.participant,
                });
                this.newMessage = '';
                console.log(this.messages)
            },
            sendTypingEvent() {
                Echo.join('chat.' + this.chat_id)
                    .whisper('typing', this.user);
            },
            currentTime() {
                // let current = new Date()
                // let date = current.getFullYear() + '-' + (current.getMonth()+1) + '-' + current.getDate()
                // let time = current.getHours() + ":" + current.getMinutes() + ":" + current.getSeconds()
                // let dateTime = date +' '+ time
                var now = new Date();
                let hours = ("0" + now.getHours()).slice(-2)
                let minutes = ("0" + now.getMinutes()).slice(-2)
                let seconds = ("0" + now.getSeconds()).slice(-2)
                var day = ("0" + now.getDate()).slice(-2);
                var month = ("0" + (now.getMonth() + 1)).slice(-2);
                var today = now.getFullYear() + "-" + (month) + "-" + (day) + ' ' + hours + ':' + minutes + ':' + seconds;
                console.log(today)
                return today;
            }
        }
    }
</script>