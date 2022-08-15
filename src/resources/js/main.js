//main javascript apps, executed before vue instance inisiated dan before main.js on project executed
//can be used to register vue global componet
import globals from "@/globals";

// jika fitur notifikasi aktif maka registerkan component navbar notifikasi
if(
    globals().AppConfig.packageLocal.moduser.notification.enable==1 && 
    globals().AppConfig.packageLocal.moduser.notification.show==1
){
    Vue.component('notif-navbar',require("./views/admin/user/notifnavbar").default);
}

// load default userprofile
const defaultProfile = resolve => {
    require.ensure(["./views/admin/user/profiletab/profile"], () => {
        resolve(require("./views/admin/user/profiletab/profile"));
    });
};
// load userprofile per project
const customProfile = resolve => {
    require.ensure(["node_modules/../app/MainApp/resources/js/components/moduser/profiletab/profile"], () => {
        resolve(require("node_modules/../app/MainApp/resources/js/components/moduser/profiletab/profile"));
    });
};

if(globals().AppConfig.packageLocal.moduser.user_profile_tab.profile.show==1){
    if(globals().AppConfig.packageLocal.moduser.user_profile_tab.profile.is_custom==1){
        Vue.component('profile-tab-profile',profileInternal);
    }else{
        Vue.component('profile-tab-profile',defaultProfile);
    }
}