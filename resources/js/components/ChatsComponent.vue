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
        props: ['chats'],
        data() {
            return {
                chats: [],
            }
        },
        mounted() {
        },
    }
</script>
