<template>
  <div>
        
    <h4 class="d-flex justify-content-between align-items-center w-100 mb-4">
      <router-link class="btn btn-outline-success d-block" :to="{name: 'role.list'}">
        <span class="ion ion-ios-arrow-back"></span>&nbsp; Kembali
      </router-link>
      <div>
        <span class="text-muted font-weight-light">Roles /</span>
        Role Form
      </div>
    </h4>

    <b-card>
      <b-card-body class="pb-2">

        <b-form-group label="Role Name">
          <b-input />
        </b-form-group>

        <b-form-group label="Instansi">
          <b-select value="1" :options="{1: 'ALL', 2: 'Owner (BPKA)', 3: 'Dinas', 4: 'Bidang', 5: 'Unit Kerja'}" />
        </b-form-group>

      </b-card-body>
      
      <hr class="border-light m-0">

      <div class="table-responsive">

        <b-table :items="userData.permissions" class="card-table m-0">
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
      
      <div class="text-right mt-3">
        <b-btn variant="primary">Save changes</b-btn>&nbsp;
        <b-btn variant="default">Cancel</b-btn>
      </div>
    </b-card>
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
  components: {
    Multiselect
  },
  data: () => ({
    userData: {
      avatar: '5-small.png',
      name: 'Nelle Maxwell',
      username: 'nmaxwell',
      email: 'nmaxwell@mail.com',
      company: 'Company Ltd.',
      id: 3425433,
      verified: true,
      role: 1,
      status: 1,

      permissions: [
        { module: 'Master', read: true, write: false, create: false, delete: false },
        { module: 'User Managemen', read: true, write: true, create: true, delete: false },
        { module: 'Pengajuan', read: true, write: false, create: false, delete: true }
      ],

      info: {
        birthday: 'May 3, 1995',
        country: 'Canada',
        languages: ['English'],
        phone: '+0 (123) 456 7891',
        website: '',
        music: ['Rock', 'Alternative', 'Electro', 'Drum & Bass', 'Dance'],
        movies: ['The Green Mile', 'Pulp Fiction', 'Back to the Future', 'WALL·E', 'Django Unchained', 'The Truman Show', 'Home Alone', 'Seven Pounds'],

        twitter: 'https://twitter.com/user',
        facebook: 'https://www.facebook.com/user',
        google: '',
        linkedin: '',
        instagram: 'https://www.instagram.com/user'
      }
    }
  }),
  methods: {
    addMusicTag (newTag) {
      this.userData.info.music.push(newTag)
    },
    addMovieTag (newTag) {
      this.userData.info.movies.push(newTag)
    }
  }
}
</script>
