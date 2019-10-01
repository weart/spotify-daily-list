<template>
  <div class="q-pa-md">
    <q-layout view="lHh lpr lFf" container class="shadow-2 rounded-borders" style="height: 1000px" >
      <q-header bordered class="bg-white text-primary">
        <q-toolbar>
          <q-toolbar-title class="text-center">
            Discoveryfy
          </q-toolbar-title>
          <q-btn push icon="account_box" type="a">
            {{ $t('Login') }}
          </q-btn>
        </q-toolbar>
      </q-header>

      <q-footer bordered class="bg-white text-primary" reveal>
        <q-tabs
          no-caps active-color="primary" indicator-color="transparent"
          class="text-grey">
          <q-tab name="gavel" label="Polls" />
          <q-tab name="supervisor_account" label="Organizations" />
        </q-tabs>
      </q-footer>

      <q-page-container>
        <router-view />
      </q-page-container>
    </q-layout>
  </div>
</template>

<script>
export default {
  name: 'IosLayout',
  data() {
    return {
      tab: 'gavel',
    };
  },
};
</script>
