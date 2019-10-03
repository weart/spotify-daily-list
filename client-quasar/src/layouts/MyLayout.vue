<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          color="primary"
          icon="gavel"
          label="Polls"
          to="/polls"
        />
        <q-btn
          icon="supervisor_account"
          label="Groups"
          to="/orgs"
        />
        <q-space />
        <q-toolbar-title
          class="text-center"
          to="/"
        >
          Discoveryfy: {{ this.$route.name }}
        </q-toolbar-title>
        <q-space />

        <q-btn
          @click="openLoginDialog"
          :ripple="{ center: true }"
          icon="account_box"
        >
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
      <login-dialog
        ref="loginDialog"
        v-model="loginDialog"
      />
      <register-dialog
        ref="registerDialog"
        v-model="registerDialog"
      />
    </q-page-container>
  </q-layout>
</template>

<script>
import LoginDialog from 'src/components/LoginDialog';
import RegisterDialog from "../components/RegisterDialog";

export default {
  name: 'MyLayout',
  components: {
    LoginDialog,
    RegisterDialog,
  },
  data() {
    return {
      loginDialog: false,
      registerDialog: false,
    };
  },
  mounted() {
    this.$root.$on('openLoginDialog', this.openLoginDialog);
    this.$root.$on('openRegisterDialog', this.openRegisterDialog);
  },
  beforeDestroy() {
    this.$root.$off(['openLoginDialog','openRegisterDialog']);
  },
  methods: {
    openLoginDialog() {
      this.$refs.loginDialog.open();
    },
    openRegisterDialog() {
      this.$refs.registerDialog.open();
    }
  },
};
</script>
