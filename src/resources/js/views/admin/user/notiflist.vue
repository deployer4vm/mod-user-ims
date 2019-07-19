<template>
    <b-card no-body header="Notification">      
        <b-list-group>
            
            <b-list-group-item :to="{name: item.link_web.route, params: item.link_web.parameter}" class="media d-flex align-items-center" v-for="item in notif.notification" :key="'pagenotif-' + item.id">
                <div :class="'ui-icon ui-icon-sm ion ion-ios-text border-0 text-white ' + (item.read_at==null?'bg-danger':'bg-secondary')"></div>
                <div class="media-body line-height-condenced ml-3">
                    <div :class="item.read_at==null?'text-dark font-weight-bold':'text-muted'">{{item.data.subject}}</div>
                    <div class="text-light small mt-1">{{item.data.description}}</div>
                    <div class="text-light small mt-1">{{item.created_at}}</div>
                </div>
            </b-list-group-item>
        </b-list-group>
        <div class="float-right m-2 mt-4" v-if="showPagination">            
            <b-pagination size="sm" :total-rows="notif.summary.count" v-model="currentPage" :per-page="perPage" align="center" />
        </div>
    </b-card>
</template>
<script>
export default {
    data() {
        return {
            notif:{
              notification: [],
              summary: {
                count: 0,
                unread_count: 0
              }
            },
            currentPage:1,
            perPage:10,
            
        };
    },
    computed: {
        showPagination() {
            return this.notif.summary.count > this.perPage;
        }
    },
    watch: {
        currentPage(nV,oV) {
            this.loadNotif((nV-1) * this.perPage);
        }
    },
    created() {
        this.loadNotif();
    },
    methods: {
        loadNotif(offset=0) {
          let filterParams = {params: {offset: offset,limit: this.perPage, setRead: true}};
          this.LocalApi.get(this.AppConfig.endpoint.api.moduser + "/notification" , filterParams)
            .then(res => {
                this.notif = res.data.data;
            });
        }
    }
};
</script>
