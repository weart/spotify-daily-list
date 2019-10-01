<template v-if="organization && organization.members.length > 0">
  <q-list bordered padding>
    <!-- eslint-disable vue/valid-v-for -->
    <template v-for="member in organization.membersRaw">

      <q-item v-bind:key="member.id" v-bind:member="member">
        <q-item-section>
          <q-item-label>{{ member.artist }}</q-item-label>
          <q-item-label caption lines="2">{{ member.name }}</q-item-label>
        </q-item-section>

        <q-item-section side top>
          <q-item-label>{{ member.rol }}</q-item-label>
        </q-item-section>

      </q-item>

      <q-separator spaced />
    </template>
  </q-list>
</template>

<script>
export default {
  name: 'TracksList',
  props: [
    'organization',
  ],
  methods: {
    onSendVote() {
      console.log('vote!');
      // this.$router.push({ name: 'Poll', params: { id: this.poll.id } });
    },
  },
};
</script>
