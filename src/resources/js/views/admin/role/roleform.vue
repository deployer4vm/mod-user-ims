<template>
  <div>
    
    <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
      <router-link class="btn btn-outline-success d-block" :to="{name: 'pengajuan'}">
        <span class="ion ion-ios-arrow-back"></span>&nbsp; Kembali
      </router-link>
      <div>
        <span class="text-muted font-weight-light">Roles /</span>
        {{ title }}
      </div>
    </h4>

    <b-tabs class="nav-tabs-top nav-responsive-sm">
      <b-tab title="Account" active>
        <b-card-body>
          
          <b-form-group label="Name">
            <b-input v-model="userData.name" />
          </b-form-group>

          <b-form-group label="Level">
            <b-select v-model="userData.status" :options="{1: 'Active', 2: 'Banned', 3: 'Deleted'}" />
          </b-form-group>

        </b-card-body>
        <hr class="border-light m-0">
        <div class="table-responsive">

          <b-table :items="form.permissions" class="card-table m-0">
            <template slot="read" slot-scope="data">
              <b-check v-model="data.item.read" class="px-2 m-0" />
            </template>
            <template slot="write" slot-scope="data">
              <b-check v-model="data.item.write" class="px-2 m-0" />
            </template>
            <template slot="create" slot-scope="data">
              <b-check v-model="data.item.create" class="px-2 m-0" />
            </template>
            <template slot="delete" slot-scope="data">
              <b-check v-model="data.item.delete" class="px-2 m-0" />
            </template>
          </b-table>

        </div>
      </b-tab>
    </b-tabs>

    <div class="text-right mt-3">
      <b-btn variant="primary">Save changes</b-btn>&nbsp;
      <b-btn variant="default">Cancel</b-btn>
    </div>
  </div>
</template>

<style src="node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>

<!-- Page -->
<style src="@/vendor/styles/pages/users.scss" lang="scss"></style>

<script>
import Multiselect from 'node_modules/vue-multiselect'

export default {
  name: 'pages-user-edit',
  metaInfo: {
    title: 'User edit - Pages'
  },
  components: {
    Multiselect
  },
  data: () => ({
    form: {
      id: 3425433,
      name: 'Nelle Maxwell',
      level: 1,
      rule: null,
      permissions: [
        { module: 'Users', read: true, write: false, create: false, delete: false },
        { module: 'Articles', read: true, write: true, create: true, delete: false },
        { module: 'Staff', read: false, write: false, create: false, delete: false }
      ]
    }
  }),
  computed: {    
    title() {
      return this.isAdd ? "Tambah user baru" : "#" + this.form.name;
    }
  },
  methods: {
    showUserField(field){
      return !this.AppConfig.packageLocal.moduser.users_hidden_field.include(field);
    },
    showProfileField(field){
      return !this.AppConfig.packageLocal.moduser.user_profiles.hide.include(field);
    },
    addMusicTag (newTag) {
      this.userData.info.music.push(newTag)
    },
    addMovieTag (newTag) {
      this.userData.info.movies.push(newTag)
    }
  }
}
</script>
