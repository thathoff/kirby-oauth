<template>
  <div>
    <k-login
      :methods="methods"
      v-if="settings.enabled === false || settings.onlyOauth === false"
    />
    <OAuth
      v-if="settings.enabled === true"
      :providers="providers"
      :error="error"
    />
  </div>
</template>

<script>
import OAuth from "./OAuth.vue";

export default {
  components: {
    OAuth
  },
  props: {
    methods: Array,
    pending: Object
  },
  data() {
    return {
      settings: {},
      error: null
    };
  },
  created() {
    this.load();
  },
  computed: {
    providers () {
      return Object.values(this.settings.providers)
    }
  },
  methods: {
    async load() {
      this.settings = await this.$api.get("oauth/settings");
      this.error = (await this.$api.get("oauth/oauthError")).msg;
    }
  }
};
</script>
