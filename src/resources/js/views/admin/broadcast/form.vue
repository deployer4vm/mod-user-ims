<template>
  <div>
    <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
      <div>Broadcast notifikasi</div>
    </h4>

    <b-card>
        <b-form-row>
          <b-form-group label="Judul notifikasi" class="col position-relative">
            <b-input v-model.trim="form.title" placeholder="" />
          </b-form-group>          
        </b-form-row>

        <b-form-row>
          <b-form-group label="Deskripsi notifikasi singkat" class="col position-relative">              
            <b-textarea v-model.trim="form.description" placeholder="" rows="2" />
          </b-form-group>        
        </b-form-row>

        <b-form-row>
          <b-form-group label="Isi Pesan / Notifikasi" class="col position-relative"> 
            <quill-editor v-model="form.message" :options="editorOptions" />
          </b-form-group>        
        </b-form-row>

        <b-form-row class="text-right">   
            <b-btn @click="sendBroadcast">Send Broadcast</b-btn>
        </b-form-row>

    </b-card>

  </div>
</template>

<style src="@/vendor/libs/vue-quill-editor/typography.scss" lang="scss"></style>
<style src="@/vendor/libs/vue-quill-editor/editor.scss" lang="scss"></style>

<script>
export default {
    name: "user-broadcast",

    components: {
        quillEditor: () => import('node_modules/vue-quill-editor/dist/vue-quill-editor').then(m => m.quillEditor).catch(() => {})
    },
    data: () => ({
        
        editorOptions: {
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] },{ 'size': [] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            }
        },
        userApi: '',
        form: {
            title: '',
            description: '',
            message: ''
        }
    }),
    computed: {
    },

    methods: {
        sendBroadcast(){

            if(!this.UserAuth.hasAccess('moduser.broadcast','c')) {
                //goto dashboard current tenant
                this.Web.goToCurrentTenant();
                this.Web.showAlert({text: this.Trans.get('alert.access_denied'),style: "warning"});
                return false;
            }   

            if(this.form.title == '' || this.form.message == ''){
                this.Web.showAlert({text: "Judul dan isi notifikasi harus diisi",type: "warning"});
                return false;
            }
            this.LocalApi.post(this.userApi + '/broadcast',this.form).then(res => {
                this.form = {
                    title: '',
                    description: '',
                    message: ''
                };
                this.Web.showAlert({text: "Broadcast notif sedang diproses",type: "success"});
            }).catch(err => {
                this.Web.showAlert({text: "Broadcast notif gagal : ".err.message,type: "warning"});
            });
        }
    },
    created() {
        if(!this.UserAuth.hasAccess('moduser.broadcast')) {
            //goto dashboard current tenant
            this.Web.goToCurrentTenant();
            this.Web.showAlert({text: this.Trans.get('alert.access_denied'),style: "warning"});
            return false;
        }   

        this.userApi = this.AppConfig.endpoint.api.moduser;
    }
};
</script>