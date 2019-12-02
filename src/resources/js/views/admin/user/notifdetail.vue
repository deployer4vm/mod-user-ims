<template>
    <b-card :header="'Notification / ' + title">
        <h3>{{ notif.data.subject}}</h3>  
        <b-card-text>
            {{ notif.data.body}}
        </b-card-text>
        <hr>
        <b-button :to="{name: 'notification'}" variant="primary">Back</b-button>
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
