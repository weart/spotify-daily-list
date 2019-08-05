import Vue from 'vue';
import Vuex from 'vuex';

import poll from 'src/store/modules/poll/';

Vue.use(Vuex);

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation
 */

export default function (/* { ssrContext } */) {
  const Store = new Vuex.Store({
    modules: {
      poll,
    },

    // enable strict mode (adds overhead!)
    // for dev mode only
    // strict: process.env.DEV,
    strict: false,
    devtools: true,
  });

  return Store;
}
