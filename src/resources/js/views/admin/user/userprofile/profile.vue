<template>
    <div>
        <b-card-body class="pb-2">
            <b-form-group label="Bio">
              <b-textarea v-model="accountData.info.bio" rows="5" />
            </b-form-group>

            <b-form-group label="Birthday">
              <b-input v-model="accountData.info.birthday" />
            </b-form-group>

            <b-form-group label="Country">
              <b-select
                v-model="accountData.info.country"
                :options="['USA', 'Canada', 'UK', 'Germany', 'France']"
              />
            </b-form-group>

            <b-form-group label="Languages">
              <multiselect
                v-model="accountData.info.languages"
                :multiple="true"
                :options="['English', 'German', 'French']"
              />
            </b-form-group>
          </b-card-body>
          <hr class="border-light m-0" />
          <b-card-body class="pb-2">
            <h6 class="mb-4">Contacts</h6>

            <b-form-group label="Phone">
              <b-input v-model="accountData.info.phone" />
            </b-form-group>

            <b-form-group label="Website">
              <b-input v-model="accountData.info.website" />
            </b-form-group>
          </b-card-body>
          <hr class="border-light m-0" />
          <b-card-body class="pb-2">
            <h6 class="mb-4">Interests</h6>

            <b-form-group label="Favorite music">
              <multiselect
                v-model="accountData.info.music"
                :multiple="true"
                :taggable="true"
                :options="[]"
                @tag="addMusicTag"
                placeholder="Add tag"
              />
            </b-form-group>

            <b-form-group label="Favorite movies">
              <multiselect
                v-model="accountData.info.movies"
                :multiple="true"
                :taggable="true"
                :options="[]"
                @tag="addMovieTag"
                placeholder="Add tag"
              />
            </b-form-group>
          </b-card-body>
        </div>
</template>
<script>
export default {
    
}
</script>
