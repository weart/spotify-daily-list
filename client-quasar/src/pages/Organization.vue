<template>
<q-page>
<!--  <q-ajax-bar ref="bar" position="top" color="accent" size="10px" skip-hijack />-->

  <q-toolbar class="q-my-md">
    <q-breadcrumbs class="q-mr-sm">
      <q-breadcrumbs-el
        v-for="(breadcrumb, idx) in breadcrumbList"
        :key="idx"
        :label="breadcrumb.label"
        :icon="breadcrumb.icon"
        :to="breadcrumb.to"
      />
      <q-breadcrumbs-el :label="item.name" />
    </q-breadcrumbs>
    <q-space />
    <div><q-btn flat round dense icon="add" @click="organizationCreate" /></div>
  </q-toolbar>

  <!--
  <q-spinner-audio
    v-if="isLoading"
    color="primary"
    size="2em"
  />
  -->

  <q-card flat bordered class="center-card">

    <q-toolbar>
      <q-card-section class="card-title">
        <div>Members</div>
      </q-card-section>
      <q-space />
      <q-btn flat rounded icon="add" label="Invite new members" />
    </q-toolbar>

    <q-separator />

    <q-card-section style="max-height: 50vh" class="scroll">
      <template v-if="!item || !item.members || item.members.length == 0">
        <h5>Without members</h5>
      </template>
      <template v-if="item && item.members.length > 0">
        <members-list v-bind:organization="item"></members-list>
      </template>
    </q-card-section>

  </q-card>

  <br /><br />

  <q-card flat bordered class="center-card">

    <q-toolbar>
      <q-card-section class="card-title">
        <div>Properties</div>
      </q-card-section>
      <q-space />
      <q-btn flat rounded icon="delete_forever" color="warning" label="Delete poll">
        <q-tooltip>Delete group</q-tooltip>
      </q-btn>
      <q-btn flat rounded icon="save" label="Save changes">
        <q-tooltip>Save changes</q-tooltip>
      </q-btn>

    </q-toolbar>

    <q-separator />

    <q-card-section style="max-height: 50vh" class="scroll">
      <q-form v-if="item">
        <q-toggle label="Readonly"
                  v-model="readonly" color="primary" keep-color />
        <q-toggle label="Disable"
                  v-model="disable" color="primary" keep-color />
        <q-input label="id"
                 v-model="item.id" :readonly="readonly" :disable="disable" />
        <q-input label="name"
                 v-model="item.name" :readonly="readonly" :disable="disable" />
        <q-input label="created at"
                 v-model="item.createdAt" :readonly="readonly" :disable="disable" />

        <br />
        <br />

        <q-list>
          <q-item>
            <q-item-section avatar>
              <q-item-label>
                Is this organization public and can be seen be anyone?
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-btn-toggle v-model="item.publicVisibility" spread rounded no-caps
                :readonly="readonly" :disable="disable" :options="[
                  {label: 'Yes', value: true},
                  {label: 'No', value: false},
                ]"
              />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section avatar>
              <q-item-label>
                Can anyone be part of this organization? Or an invitation is required?
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-btn-toggle v-model="item.publicMembership" spread rounded no-caps
                :readonly="readonly" :disable="disable" :options="[
                  {label: 'Yes', value: true},
                  {label: 'No', value: false},
                ]"
              />
            </q-item-section>
          </q-item>
          <q-item>
            <q-item-section avatar>
              <q-item-label>
                Minimum membership rol for allow create new polls
              </q-item-label>
            </q-item-section>
            <q-item-section>
              <q-btn-toggle v-model="item.canCreatePolls" spread rounded no-caps
                :readonly="readonly" :disable="disable" :options="[
                  {label: 'Owner', value: 0},
                  {label: 'Admin', value: 1},
                  {label: 'Members', value: 2},
                  {label: 'Anyone', value: 4},
                ]"
              />
            </q-item-section>
          </q-item>
        </q-list>
      </q-form>
    </q-card-section>
  </q-card>

</q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import membersList from 'src/components/MembersList';

export default {
  name: 'Organization',
  components: {
    membersList,
  },
  data() {
    return {
      readonly: false,
      disable: false,
    };
  },
  // Vuex
  computed: mapGetters({
    deleteError: 'poll/del/error',
    error: 'poll/show/error',
    isLoading: 'poll/show/isLoading',
    item: 'poll/show/retrieved',
  }),

  beforeDestroy() {
    this.reset();
  },
  created() {
    this.breadcrumbList = this.$route.meta.breadcrumb;
    this.retrieve(`/organizations/${decodeURIComponent(this.$route.params.id)}`);
  },

  watch: {
    error(message) {
      this.$q.notify({
        message,
        color: 'red',
        icon: 'error',
        closeBtn: this.$t('Close'),
      });
    },

    deleteError(message) {
      this.$q.notify({
        message,
        color: 'red',
        icon: 'error',
        closeBtn: this.$t('Close'),
      });
    },
  },

  methods: {
    ...mapActions({
      del: 'poll/del/del',
      reset: 'poll/show/reset',
      retrieve: 'poll/show/retrieve',
    }),

    deleteItem() {
      // eslint-disable-next-line
      if (window.confirm(this.$t('Are you sure you want to delete this group?'))) {
        this.del(this.item).then(() => this.$router.push({ name: 'Organizations' }));
      }
    },

    // Not vuex
    organizationCreate() {
      console.log('@todo org create');
      // put /orgs & go
      // this.$router.push({ name: 'Organization', params: { id: this.organization.id } });
    },
  },
};
</script>
