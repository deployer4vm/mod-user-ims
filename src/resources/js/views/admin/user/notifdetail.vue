<template>
    <b-card :header="Trans.get('notif.notification_title') + ' / ' + title">
        <!-- <h3>{{ notif.data.subject}}</h3>  -->
        <b-card-text>
            <div v-html="notif.data.body"></div>
        </b-card-text>
        <hr>
        <b-button :to="{name: 'notification'}" variant="primary">{{Trans.get('lang.back')}}</b-button>
    </b-card>
</template>
<script>
export default {
    data() {
        return {
            notif:{
                data:{
                    subject: '',
                    body: ''
                }
            }            
        };
    },
    computed: {
        title() {
            return this.notif.data.subject;
        },
        notifId() {
            return this.$route.params.notifId;
        }
    },
    created() {
        this.loadNotif();
    },
    methods: {
        loadNotif(offset=0) {
          this.LocalApi.get(this.AppConfig.endpoint.api.moduser + "/notification/" + this.notifId)
            .then(res => {
                this.notif = res.data.data;
            });
        }
    }
};
</script>
