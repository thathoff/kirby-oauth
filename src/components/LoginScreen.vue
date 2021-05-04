<template>
  <div>
    <k-login
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
import OAuth from "./OAuth";

export default {
  components: {
    OAuth
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
