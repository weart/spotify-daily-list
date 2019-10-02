import { error, success } from '../../utils/notify';
import { extractDate } from '../../utils/dates';

export default {
  created() {
    this.onRequest({
      pagination: this.pagination,
    });
  },
  data() {
    return {
      pagination: {
        sortBy: null,
        descending: false,
        page: 1, // page to be displayed
        rowsPerPage: 3, // maximum displayed rows
        rowsNumber: 10, // max number of rows
      },
      nextPage: null,
      filters: {},
    };
  },
  watch: {
    error(message) {
      message && error(message, this.$t('Close'));
    },

    items() {
      this.pagination.page = this.nextPage;
      this.pagination.rowsNumber = this.totalItems;
      this.nextPage = null;
    },

    deletedItem(val) {
      success(`${val['@id']} ${this.$t('Deleted')}.`, this.$t('Close'));
    },
  },

  methods: {
    onRequest(props) {
      const {
        pagination: { page, rowsPerPage: itemsPerPage, sortBy, descending },
      } = props;
      this.nextPage = page;
      let params = {
        ...this.filters,
      };
      if (itemsPerPage > 0) {
        params = { ...params, itemsPerPage, page };
      }
      if (sortBy) {
        params[`order[${sortBy}]`] = descending ? 'DESC' : 'ASC';
      }
      this.getPage({ params }).then(() => {
        this.pagination.sortBy = sortBy;
        this.pagination.descending = descending;
        this.pagination.rowsPerPage = itemsPerPage;
      });
    },

    formatDateTime(val, format) {
      return val ? this.$d(extractDate(val), format) : '';
    },

    onSendFilter() {
      this.onRequest({
        pagination: this.pagination,
      });
    },

    resetFilter() {
      this.filters = {};
    },

    addHandler() {
      this.$router.push({ name: `${this.$options.servicePrefix}Create` });
    },

    showHandler(item) {
      this.$router.push({ name: `${this.$options.servicePrefix}Show`, params: { id: item['@id'] } });
    },

    editHandler(item) {
      this.$router.push({ name: `${this.$options.servicePrefix}Update`, params: { id: item['@id'] } });
    },

    deleteHandler(item) {
      this.deleteItem(item).then(() =>
        this.onRequest({
          pagination: this.pagination,
        }),
      );
    },
  },
};
