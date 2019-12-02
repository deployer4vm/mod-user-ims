<template>
  <b-nav-item-dropdown no-caret :right="!isRTL" class="demo-navbar-notifications mr-lg-3">
    <template slot="button-content">
          <i class="ion ion-md-notifications-outline navbar-icon align-middle"></i>
          <span class="badge badge-danger badge-dot indicator" v-if="notif.summary.unread_count != 0"></span>
          <span class="d-lg-none align-middle">&nbsp; Notifications</span>
    </template>

    <div class="bg-primary text-center text-white font-weight-bold p-3" v-if="notif.summary.unread_count != 0">{{notif.summary.unread_count}} Unread Notifications</div>

    <b-list-group flush>
        <b-list-group-item class="media d-flex align-items-center" 
            :to="{name: item.link_web.route, params: item.link_web.parameter}" 
            v-for="item in notif.notification" :key="'navbarnotif-' + item.id"
        >
            <div :class="'ui-icon ui-icon-sm ion ion-ios-text border-0 text-white ' + (item.read_at==null?'bg-danger':'bg-secondary')"></div>
            <div class="media-body line-height-condenced ml-3">
                <div :class="item.read_at==null?'text-dark font-weight-bold':'text-muted'">{{item.data.subject}}</div>
                <div class="text-light small mt-1">{{item.data.description}}</div>
                <div class="text-light small mt-1">{{item.created_at}}</div>
            </div>
        </b-list-group-item>

    </b-list-group>

    <router-link :to="{name: 'notification'}"
      class="d-block text-center text-light small p-2 my-1"
    >Show all notifications</router-link>
  </b-nav-item-dropdown>
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
            lastNotifCount: 0
        };
    },
    created() {
        this.loadNotif();
        this.invervalNotif = setInterval(()=>{
            if(this.UserAuth.isLogin())
                this.loadNotif();
        },60000);
    },
    methods: {
        loadNotif() {
            var that = this;
          let filterParams = {params: {limit: 5}};
          this.LocalApi.get(this.AppConfig.endpoint.api.moduser + "/notification" , filterParams)
            .then(res => {
                that.notif = res.data.data;
                if(that.notif.summary.unread_count > that.lastNotifCount){                    
                    _.forEach(that.notif.notification,(v,i)=>{  
                        if(v.read_at==null){
                            that.Web.showAlert({ title:"Notifikasi Baru", text: v.data.subject });
                            console.log(v);
                        }
                    });
                }
                that.lastNotifCount = that.notif.summary.unread_count;
            });
        }
    }
};
</script>
