<template>
  <q-page>
<!--  <q-page class="flex flex-center"><img alt="Quasar logo" src="~assets/quasar-logo-full.svg">-->
    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <h4 class="q-ma-sm col-12 text-weight-bold">
        Share & Rate Songs
      </h4>
      <h5 class="q-ma-sm col-12">
        Create collaborative playlists in Spotify with
        your friends and colleagues and rate the songs
      </h5>
    </div>

    <q-spinner-audio
      v-if="isLoading"
      color="primary"
      size="2em"
    />

    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <q-btn
        push color="secondary" label="Create new organization" :ripple="{ center: true }"
        to="/organization/create"
      />
    </div>

    <div class="row q-pa-md q-gutter-md justify-around items-start">

      <organizationCard
        v-for="organization in organizations"
        v-bind:key="organization.id"
        v-bind:organization="organization"
      ></organizationCard>

    </div>

  </q-page>
</template>

<style>
</style>

<script>
import fetchSimple from 'src/utils/fetch_simple';
import organizationCard from 'src/components/OrganizationCard';

export default {
  name: 'Organizations',
  components: {
    organizationCard,
  },
  data() {
    return {
      isLoading: true,
      organizations: [],
    };
  },
  created() {
    fetchSimple('organizations').then((data) => {
      this.organizations = data;
      this.isLoading = false;
    });
  },
};
</script>
