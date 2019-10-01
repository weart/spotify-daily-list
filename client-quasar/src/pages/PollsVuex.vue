<template>
  <q-page><!--<q-page class="flex flex-center">-->
    <q-ajax-bar ref="bar" position="top" color="accent" size="10px" skip-hijack />

    <!--<img alt="Quasar logo" src="~assets/quasar-logo-full.svg">-->
    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <h5 class="q-ma-sm col-12 text-weight-bold">
        Welcome to Discoveryfy
      </h5>
      <span class="q-ma-sm col-12">Share & Rate Songs</span>
      <span class="q-ma-sm col-12">
        Create collaborative playlists in Spotify with
        your friends and colleagues and rate the songs
      </span>
    </div>

    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <q-btn
        push color="accent" label="Create new poll" :ripple="{ center: true }"
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
        v-for="poll in items"
        v-bind:key="poll.id"
        v-bind:poll="poll"
      ></pollCard>
      <!--
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
import { mapActions, mapGetters } from 'vuex';
import pollCard from 'src/components/PollCard';
// import cardTitle from "src/components/CardTitle";

export default {
  name: 'Polls',
  props: [],
  components: {
    pollCard,
    // cardTitle,
  },
  created() {
    this.onRequest({
      pagination: this.pagination,
      filter: undefined,
    });
  },
  data() {
    return {
      // isLoading: true,
      polls: [],
      // pastPolls: [],
      // currentPolls: [],
      // Vuex
      pagination: {
        // sortBy: 'name',
        // descending: false,
        page: 1, // page to be displayed
        rowsPerPage: 3, // maximum displayed rows
        rowsNumber: 10, // virtualy the max number of rows
      },
      nextPage: null,
    };
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
    /**
     * VUEX
     */
    isLoading(val) {
      if (val) {
        this.$refs.bar.start();
      } else {
        this.$refs.bar.stop();
      }
    },

    error(message) {
      this.$q.notify({
        message,
        color: 'red',
        icon: 'error',
        closeBtn: this.$t('Close'),
      });
    },

    items() {
      this.pagination.page = this.nextPage;
      this.nextPage = null;
    },

    deletedItem(val) {
      this.$q.notify({
        message: `${val['@id']} ${this.$t('deleted')}.`,
        color: 'green',
        icon: 'tag_faces',
        closeBtn: this.$t('Close'),
      });
    },

    totalItems(val) {
      this.pagination.rowsNumber = val;
    },
  },
  computed: mapGetters({
    deletedItem: 'poll/del/deleted',
    error: 'poll/list/error',
    items: 'poll/list/items',
    isLoading: 'poll/list/isLoading',
    view: 'poll/list/view',
    totalItems: 'poll/list/totalItems',
  }),

  methods: {
    ...mapActions({
      getPage: 'poll/list/default',
    }),

    onRequest(props) {
      console.log('onRequest');
      const { page, rowsPerPage } = props.pagination;
      this.nextPage = page;
      this.getPage({ params: { itemsPerPage: rowsPerPage, page } });
    },
  },
};
</script>
