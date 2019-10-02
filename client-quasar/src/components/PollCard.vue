<template>
  <q-card
    class="my-card"
    @click="goToPoll"
  >
    <q-img :src="imgSrc">
      <div class="absolute-bottom">
        <div class="text-h6">
          {{ poll.name }}
        </div>
        <!--<div class="text-subtitle2">Org: {{ poll.organization.name }}</div>-->
        <div class="text-subtitle2">
          Num Tracks: {{ poll.tracks.length }}
        </div>
        <div class="text-subtitle2">
          Num Votes: {{ poll.votes.length }}
        </div>
      </div>
    </q-img>
  </q-card>
</template>

<style scoped>
.my-card {
  width: 100%;
  max-width: 400px;
}
</style>

<script>
export default {
  name: 'PollCard',
  props: {
    poll: {
      type: Object,
      default: null
    },
  },
  methods: {
    goToPoll() {
      this.$router.push({ name: 'Poll', params: { id: this.poll.id } });
    },
  },
  computed: {
    imgSrc() {
      // return 'https://cdn.quasar.dev/img/parallax2.jpg';
      return (
        this.poll.length > 0
        && this.poll.spotifyPlaylistImages.length > 0
        && this.poll.spotifyPlaylistImages[0].length > 0
        && this.poll.spotifyPlaylistImages[0].url.length > 0
      )
        ? this.poll.spotifyPlaylistImages[0].url : 'https://cdn.quasar.dev/img/parallax2.jpg';
    },
  },
};
</script>
