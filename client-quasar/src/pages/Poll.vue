<template>
<div>
  <q-toolbar class="q-my-md">
    <q-breadcrumbs class="q-mr-sm">
      <q-breadcrumbs-el
        v-for="(breadcrumb, idx) in breadcrumbList"
        :key="idx"
        :label="breadcrumb.label"
        :icon="breadcrumb.icon"
        :to="breadcrumb.to"
      />
      <q-breadcrumbs-el label="Poll #name" />
    </q-breadcrumbs>
    <q-space />
    <div><q-btn flat round dense icon="add" @click="pollCreate" /></div>
  </q-toolbar>

  <q-spinner-audio
    v-if="isLoading"
    color="primary"
    size="2em"
  />

  <q-card flat bordered class="center-card">

    <q-toolbar>
      <q-card-section class="card-title">
        <div>Tracks</div>
      </q-card-section>
      <q-space />
      <q-btn flat round dense icon="library_music">
        <q-tooltip>Go to Spotify playlist</q-tooltip>
      </q-btn>
      <q-btn flat round dense icon="add">
        <q-tooltip>Suggest new song</q-tooltip>
      </q-btn>
      <q-btn flat round dense icon="how_to_vote"
             :loading="voteLoading" @click="voteCreate">
        <q-tooltip>Vote!</q-tooltip>
        <template v-slot:loading>
          <q-spinner-gears />
        </template>
      </q-btn>
    </q-toolbar>

    <q-separator></q-separator>

    <q-card-section style="max-height: 50vh" class="scroll">
      <tracks-list v-bind:poll="poll"></tracks-list>
    </q-card-section>

  </q-card>

  <br /><br />

  <q-card flat bordered class="center-card">

    <q-toolbar>
      <q-card-section class="card-title">
        <div>Properties</div>
      </q-card-section>
      <q-space />
      <q-btn flat round dense icon="delete_forever">
        <q-tooltip>Delete poll</q-tooltip>
      </q-btn>
      <q-btn flat round dense icon="save">
        <q-tooltip>Save changes</q-tooltip>
      </q-btn>

    </q-toolbar>

    <q-separator></q-separator>

    <q-card-section style="max-height: 50vh" class="scroll">
      <q-form>
        <!-- Basic -->
        <q-toggle label="Readonly"
          v-model="readonly" dark />
        <q-toggle label="Disable"
          v-model="disable" dark />
        <q-input label="id"
          v-model="poll.id" :readonly="readonly" :disable="disable" />
        <q-input label="name"
          v-model="poll.name" :readonly="readonly" :disable="disable" />
        <q-input label="Organization"
          v-model="poll.organization.name" :readonly="readonly" :disable="disable" />

        <!-- Timing -->
        <q-input label="startDate"
          v-model="poll.startDate" :readonly="readonly" :disable="disable" />
        <q-input label="endDate"
          v-model="poll.endDate" :readonly="readonly" :disable="disable" />
        <q-input label="restartDate"
          v-model="poll.restartDate" :readonly="readonly" :disable="disable" />

        <!-- Spotify -->
        <q-input label="spotifyPlaylistImages"
          v-model="poll.spotifyPlaylistImages" :readonly="readonly" :disable="disable" />
        <q-input label="spotifyPlaylistUri"
          v-model="poll.spotifyPlaylistUri" :readonly="readonly" :disable="disable" />
        <q-input label="spotifyWinnerPlaylistUri"
          v-model="poll.spotifyWinnerPlaylistUri" :readonly="readonly" :disable="disable" />
        <q-input label="spotifyHistoricPlaylistUri"
          v-model="poll.spotifyHistoricPlaylistUri" :readonly="readonly" :disable="disable" />

        <!-- Advanced -->
        <q-toggle label="publicVisibility"
          v-model="poll.publicVisibility" :readonly="readonly" :disable="disable" />
        <q-toggle label="anonCanVote"
          v-model="poll.anonCanVote" :readonly="readonly" :disable="disable" />
        <q-toggle label="anonCanAddTrack"
          v-model="poll.anonCanAddTrack" :readonly="readonly" :disable="disable" />
        <q-toggle label="multipleUserTracks"
          v-model="poll.multipleUserTracks" :readonly="readonly" :disable="disable" />
        <q-toggle label="multipleAnonTracks"
          v-model="poll.multipleAnonTracks" :readonly="readonly" :disable="disable" />

        <q-badge color="secondary">
          anonVotesMaxRating: {{ poll.anonVotesMaxRating }} (Between 1 and 100)
        </q-badge>
        <q-slider
          v-model="poll.anonVotesMaxRating"
          :min="0"
          :max="100"
          label
          :label-value="poll.anonVotesMaxRating + 'px'"
          label-always
          color="primary"
          :readonly="readonly" :disable="disable"
        />
        <q-badge color="secondary">
          userVotesMaxRating: {{ poll.userVotesMaxRating }} (Between 1 and 100)
        </q-badge>
        <q-slider
          v-model="poll.userVotesMaxRating"
          :min="0"
          :max="100"
          label
          :label-value="poll.userVotesMaxRating + 'px'"
          label-always
          color="primary"
          :readonly="readonly" :disable="disable"
        />
      </q-form>
    </q-card-section>

  </q-card>
</div>
</template>

<style>

</style>

<script>
import fetchSimple from 'src/utils/fetch_simple';
import tracksList from 'src/components/TracksList';

export default {
  name: 'Poll',
  props: [
    // 'poll'
  ],
  components: {
    tracksList,
  },
  data() {
    return {
      isLoading: true,
      voteLoading: false,
      voteValid: false,
      canModify: false,
      readonly: false,
      disable: false,
      poll: {
        id: 1,
        name: 'abc',
        organization: {
          name: 'cba',
        },
        tracks: {},
      },
    };
  },
  created() {
    this.breadcrumbList = this.$route.meta.breadcrumb;
    fetchSimple(`polls/${decodeURIComponent(this.$route.params.id)}`).then((data) => {
      console.log('poll', data);
      this.poll = data;
      this.isLoading = false;
    });
  },
  methods: {
    pollCreate() {
      console.log('@todo pollCreate');
      // put /polls & go
      // this.$router.push({ name: 'Poll', params: { id: this.poll.id } });
    },
    voteCreate() {
      // we set loading state
      this.voteLoading = true;
      // simulate a delay
      setTimeout(() => {
        // we're done, we reset loading state
        this.voteLoading = false;
        if (this.voteValid !== true) {
          this.$q.notify({
            message: 'Problems...',
            color: 'warning',
            icon: 'warning',
            timeout: 3000,
            position: 'bottom',
          });
        } else {
          this.$q.notify({
            message: 'Saved',
            color: 'primary',
            icon: 'cloud_done',
            timeout: 3000,
            position: 'bottom',
          });
        }
      }, 3000);
    },
  },
};
</script>
