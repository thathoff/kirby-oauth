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
      providers: [],
      error: null
    };
  },
  created() {
    this.load();
  },
  methods: {
    async load() {
      this.settings = await this.$api.get("oauth/settings");
      this.providers = Object.values(
        await this.$api.get("oauth/providers")
      );
      this.error = (await this.$api.get("oauth/oauthError")).msg;
    }
  }
};
</script>
