//main javascript apps, executed before vue instance inisiated dan before main.js on project executed
//can be used to register vue global componet
import globals from "@/globals";

if(globals().AppConfig.packageLocal.moduser.notification.enable==1 && globals().AppConfig.packageLocal.moduser.notification.show==1){
    Vue.component('notif-navbar',require("./views/admin/user/notifnavbar").default);
}