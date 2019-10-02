import Vue from 'vue';
import Vuex from 'vuex';

import poll from 'src/store/modules/poll/';
import track from 'src/store/modules/track/';
import vote from 'src/store/modules/vote/';

Vue.use(Vuex);

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation
 */

export default function (/* { ssrContext } */) {
  const Store = new Vuex.Store({
    modules: {
      poll,
      track,
      vote,
    },

    // enable strict mode (adds overhead!)
    // for dev mode only
    // strict: process.env.DEV,
    strict: false,
    devtools: true,
  });

  return Store;
}
