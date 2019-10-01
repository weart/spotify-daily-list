<template>
  <q-page><!--<q-page class="flex flex-center">-->
    <!--<img alt="Quasar logo" src="~assets/quasar-logo-full.svg">-->
    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <h4 class="q-ma-sm col-12 text-weight-bold">
        Share & Rate Songs
      </h4>
      <h5 class="q-ma-sm col-12">
        Create collaborative playlists in Spotify with
        your friends and colleagues and rate the songs
      </h5>
    </div>

    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <q-btn
        push color="secondary" label="Create new poll" :ripple="{ center: true }"
        to="/polls/create"
      />
    </div>


    <div class="row q-pa-md q-gutter-md justify-around items-start">

      <q-spinner-audio
        v-if="isLoading"
        color="primary"
        size="2em"
      />

      <h6 class="full-width text-center q-ma-xs" v-if="!isLoading">Current polls</h6>

      <pollCard
        v-for="poll in currentPolls"
        v-bind:key="poll.id"
        v-bind:poll="poll"
      ></pollCard>

      <h6 class="full-width text-center q-ma-xs" v-if="!isLoading">Past polls</h6>

      <pollCard
        v-for="poll in pastPolls"
        v-bind:key="poll.id"
        v-bind:poll="poll"
      ></pollCard>
<!--
      <cardTitle></cardTitle>
        :title="as"
        :slugifiedTitle="as"
        :prefix="as"
      -->
    </div>

  </q-page>
</template>

<style>
</style>

<script>
import fetchSimple from 'src/utils/fetch_simple';
import pollCard from 'src/components/PollCard';
// import cardTitle from "src/components/CardTitle";

export default {
  name: 'Polls',
  props: [],
  components: {
    pollCard,
    // cardTitle,
  },
  data() {
    return {
      isLoading: true,
      polls: [],
      pastPolls: [],
      currentPolls: [],
    };
  },
  created() {
    fetchSimple('polls').then((data) => {
      this.polls = data;
      this.isLoading = false;
    });
  },
  watch: {
    polls() {
      if (this.polls) {
        this.polls.forEach((poll) => {
          if (poll.endDate && poll.endDate.length > 0) {
            this.currentPolls.push(poll);
          } else {
            this.pastPolls.push(poll);
          }
        });
      }
    },
  },
};
</script>
