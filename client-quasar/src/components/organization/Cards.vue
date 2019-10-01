<template>
  <div class="row q-pa-md q-gutter-md justify-around items-start">
    <card
      v-for="organization in organizations"
      v-bind:key="organization.id"
      v-bind:organization="organization"
    ></card>

    <q-spinner-audio
      v-if="isLoading"
      color="primary"
      size="2em"
    />
  </div>
</template>

<script>
import fetchSimple from 'src/utils/fetch_simple';
import card from './Card';

export default {
  name: 'Organizations',
  components: {
    card,
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
