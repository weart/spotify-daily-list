<template>
  <div class="q-pa-md">
    <q-layout view="lHh lpr lFf" container style="height: 1000px" class="shadow-2 rounded-borders">
      <q-header elevated>

        <q-toolbar>
          <q-toolbar-title class="text-center" to="/">
            Discoveryfy
          </q-toolbar-title>
          <q-btn flat round dense icon="account_box" />
        </q-toolbar>

        <q-tabs v-model="tab">
          <q-tab name="gavel" label="Polls" />
          <q-tab name="supervisor_account" label="Organizations" />
        </q-tabs>

        <q-toolbar>
          <q-toolbar-title class="text-center" to="/">
            {{ this.$route.name }}
          </q-toolbar-title>

          <!--
          <q-btn push icon="account_box" type="a" :href="spotify_login" target="_blank">
            {{ $t('Login') }}
          </q-btn>
          <q-btn flat round dense icon="how_to_vote" />
          -->
        <q-btn flat round dense icon="add" />
        </q-toolbar>

      </q-header>

      <q-page-container>
        <router-view />
      </q-page-container>
    </q-layout>
  </div>
</template>

<script>
import { openURL } from 'quasar';
import { ENTRYPOINT } from 'src/config/entrypoint';

export default {
  name: 'CardLayout',
  data() {
    return {
      spotify_login: `${ENTRYPOINT + (ENTRYPOINT.endsWith('/') ? '' : '/')}spotify/login`,
    };
  },
  methods: {
    openURL,
  },
};
</script>

<style>
</style>
