import * as types from './mutation_types';

export default {
  [types.TRACK_DELETE_SET_DELETED](state, deleted) {
    Object.assign(state, { deleted });
  },

  [types.TRACK_DELETE_SET_ERROR](state, error) {
    Object.assign(state, { error });
  },

  [types.TRACK_DELETE_TOGGLE_LOADING](state) {
    Object.assign(state, { isLoading: !state.isLoading });
  },

  [types.TRACK_DELETE_RESET](state) {
    Object.assign(state, {
      deleted: null,
      error: '',
      isLoading: false,
    });
  },
};
