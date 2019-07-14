<template>
    <div>
        
        <b-card-body class="pb-2">

          <h6 class="mb-4">Social links</h6>

          <b-form-group label="Twitter">
            <b-input v-model="form.info.twitter" />
          </b-form-group>

          <b-form-group label="Facebook">
            <b-input v-model="form.info.facebook" />
          </b-form-group>

          <b-form-group label="Google+">
            <b-input v-model="form.info.google" />
          </b-form-group>

          <b-form-group label="LinkedIn">
            <b-input v-model="form.info.linkedin" />
          </b-form-group>

          <b-form-group label="Instagram">
            <b-input v-model="userData.info.instagram" />
          </b-form-group>

        </b-card-body>
        <hr class="border-light m-0">
        <b-card-body class="pb-2">

          <h6 class="mb-4">Personal info</h6>

          <b-form-group label="Birthday">
            <b-input v-model="userData.info.birthday" />
          </b-form-group>

          <b-form-group label="Country">
            <b-select v-model="userData.info.country" :options="['USA', 'Canada', 'UK', 'Germany', 'France']" />
          </b-form-group>

          <b-form-group label="Languages">
            <multiselect v-model="userData.info.languages" :multiple="true" :options="['English', 'German', 'French']" />
          </b-form-group>

        </b-card-body>
        <hr class="border-light m-0">
        <b-card-body class="pb-2">

          <h6 class="mb-4">Contacts</h6>

          <b-form-group label="Phone">
            <b-input v-model="userData.info.phone" />
          </b-form-group>

          <b-form-group label="Website">
            <b-input v-model="userData.info.website" />
          </b-form-group>

        </b-card-body>
        <hr class="border-light m-0">
        <b-card-body class="pb-2">

          <h6 class="mb-4">Interests</h6>

          <b-form-group label="Favorite music">
            <multiselect v-model="userData.info.music" :multiple="true" :taggable="true" :options="[]" @tag="addMusicTag" placeholder="Add tag" />
          </b-form-group>

          <b-form-group label="Favorite movies">
            <multiselect v-model="userData.info.movies" :multiple="true" :taggable="true" :options="[]" @tag="addMovieTag" placeholder="Add tag" />
          </b-form-group>

        </b-card-body>
    </div>
</template>
<script>
export default {
    
}
</script>
