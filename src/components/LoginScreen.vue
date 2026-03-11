<template>
  <div>
    <k-login
      v-bind="{ methods, value }"
      @error="onError"
      v-if="settings.enabled === false || settings.onlyOauth === false"
    />
    <OAuth
      v-if="settings.enabled === true && !this.autoLogin"
      :providers="providers"
    />
    <div v-if="this.autoLogin">{{$t('thathoff.oauth.autoLoggingIn') }}</div>
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
    pending: Object,
    value: Object
  },
  data() {
    return {
      settings: {},
      error: null,
      autoLogin: false
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

      if (this.error) {
        // mimic kirby error handling to show error message on top
        // of the login screen
        this.onError({
          message: this.error,
          details: {
            challengeDestroyed: false
          }
        });
        return;
      }

      if (this.settings.autoRedirect && this.providers.length == 1) {
        this.autoLogin = true
        window.location = this.providers[0].href;
        return;
      }
    },
    onError(error) {
      this.$emit("error", error);
    }
  }
};
</script>
