<template>
    <div>
        <b-card-body class="pb-2">
            - isi profile
        </b-card-body>
    </div>
</template>
<script>
export default {
    name: "profile-tab-profile",
    data() {
        return {
            form: [],
        };
    },
    created() {
        this.form = this.UserAuth.getUser();
    },
};
</script>
