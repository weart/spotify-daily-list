<template>
  <q-layout
    view="lHh Lpr lFf"
    class="bg-white"
  >
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
          icon="menu"
        />

        <q-toolbar-title>
          Discoveryfy: {{ this.$route.name }}
        </q-toolbar-title>
        <q-space />

        <template v-if="isAuthenticated">
          <q-btn
            to="/user"
            :ripple="{ center: true }"
            icon="account_box"
          >
            {{ $t('Profile') }}
          </q-btn>
        </template>
        <template v-else>
          <q-btn
            @click="openLoginDialog"
            :ripple="{ center: true }"
            icon="account_box"
          >
            {{ $t('Login') }}
          </q-btn>
        </template>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      content-class="bg-grey-2"
    >
      <q-list>
        <q-expansion-item
          label="Discoveryfy"
          default-opened
          expand-separator
          expand-icon-toggle
          to="/org/13959f26-d6ee-48cc-8e7c-ee4dd26b06be"
        >
          <q-item
            clickable
            tag="a"
            to="/poll/7959def3-f8f3-42f4-a434-c59aea1661bc"
          >
            <q-item-section avatar>
              <q-icon name="school" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Discoveryfy Weekly Playlist</q-item-label>
              <q-item-label caption>
                Public playlist restarted weekly
              </q-item-label>
            </q-item-section>
          </q-item>
          <q-item
            clickable
            tag="a"
            to="/poll/2a4ca6f5-1154-4a53-9815-3046d373a197"
          >
            <q-item-section avatar>
              <q-icon name="chat" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Discoveryfy Weekly Members Playlist</q-item-label>
              <q-item-label caption>
                Registered users playlist restarted weekly
              </q-item-label>
            </q-item-section>
          </q-item>
          <q-item
            clickable
            tag="a"
            to="/poll/ac920ad3-c06f-49c2-ae0f-5e40c7c03df6"
          >
            <q-item-section avatar>
              <q-icon name="record_voice_over" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Discoveryfy Monthly Playlist</q-item-label>
              <q-item-label caption>
                The best song of each week, restarted monthly
              </q-item-label>
            </q-item-section>
          </q-item>
          <q-item
            clickable
            tag="a"
            to="/poll/c8b38746-edb9-43b7-bffc-f9ff5222a6a4"
          >
            <q-item-section avatar>
              <q-icon name="record_voice_over" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Discoveryfy Monthly Member Playlist</q-item-label>
              <q-item-label caption>
                The best song of each week, restarted monthly
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>
        <q-expansion-item
          label="Sonosuite"
          default-opened
          expand-separator
          expand-icon-toggle
          to="/org/13959f26-d6ee-48cc-8e7c-ee4dd26b06be"
        >
          <q-item
            clickable
            tag="a"
            to="/poll/c8b38746-edb9-43b7-bffc-f9ff5222a6a4"
          >
            <q-item-section avatar>
              <q-icon name="school" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Sonosuite Weekly Playlist</q-item-label>
              <q-item-label caption>
                Public playlist restarted weekly
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <login-dialog
      ref="loginDialog"
      v-model="loginDialog"
    />
    <register-dialog
      ref="registerDialog"
      v-model="registerDialog"
    />
  </q-layout>
</template>

<script>
import LoginDialog from 'src/components/LoginDialog';
import RegisterDialog from "../components/RegisterDialog";
import auth from 'src/utils/auth';

export default {
  name: 'DrawerLayout',
  components: {
      LoginDialog,
      RegisterDialog,
  },
  data() {
    return {
      isAuthenticated: false,
      loginDialog: false,
      registerDialog: false,
      leftDrawerOpen: false,
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
    checkAuth() {
      auth.checkAuth(this);
      this.isAuthenticated = auth.user.authenticated;
    },
    openLoginDialog() {
      this.$refs.loginDialog.open();
    },
    openRegisterDialog() {
      this.$refs.registerDialog.open();
    }
  },
}
</script>
