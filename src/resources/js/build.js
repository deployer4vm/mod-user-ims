module.exports = function(fs, mix) {
    var filePathVendor = "vendor/hp-synapse/mod-user/src/resources/js/build/";
    var filePath = "app/MainApp/resources/js/components/moduser/";
    // jika default component belum ada maka copy
    if (!fs.existsSync(filePath + "auth/register.vue")) {
        mix.copy(filePathVendor + "auth/register.vue", filePath +"auth/register.vue");
    }
    if (!fs.existsSync(filePath + "auth/loginform.vue")) {
        mix.copy(filePathVendor + "auth/loginform.vue", filePath +"auth/loginform.vue");
    }
    if (!fs.existsSync(filePath + "auth/forgotpasswordform.vue")) {
        mix.copy(filePathVendor + "auth/forgotpasswordform.vue", filePath +"auth/forgotpasswordform.vue");
    }
    //
    if (!fs.existsSync(filePath + "userformtab/profile.vue")) {
        mix.copy(filePathVendor + "userformtab/profile.vue", filePath +"userformtab/profile.vue");
    }
    //
    if (!fs.existsSync(filePath + "profiletab/profile.vue")) {
        mix.copy(filePathVendor + "profiletab/profile.vue", filePath +"profiletab/profile.vue");
    }
};;
