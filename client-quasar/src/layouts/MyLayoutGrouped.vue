<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn-group push>
          <q-btn icon="gavel" label="Polls" to="/polls" push style="background: #00932E;" />
          <q-btn icon="supervisor_account" label="Organizations"
                 to="/orgs" push style="background: #00932E;" />
        </q-btn-group>

        <q-toolbar-title class="text-center" to="/">
          Discoveryfy: {{ this.$route.name }}
        </q-toolbar-title>
        <q-btn push icon="perm_identity" type="a" :href="spotify_login" target="_blank">
          {{ $t('Login') }}
        </q-btn>
      </q-toolbar>

      <!--
      <q-tabs inline-label class="bg-primary text-white shadow-2">
        <q-route-tab to="/polls?active=1" label="Current polls" icon="gavel">
          <q-badge color="orange" floating>20</q-badge>
        </q-route-tab>
        <q-route-tab to="/polls?active=0" label="Past polls" icon="watch_later" />
      </q-tabs>
      -->

    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>

  </q-layout>
</template>

<script>
import { openURL } from 'quasar';
import { ENTRYPOINT } from 'src/config/entrypoint';

export default {
  name: 'MyLayoutGrouped',
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
