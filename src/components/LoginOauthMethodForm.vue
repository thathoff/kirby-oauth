<template>
  <div class="k-login-form k-login-oauth-method-form">
    <p v-if="redirecting">{{ $t("thathoff.oauth.autoLoggingIn") }}</p>
    <OAuth v-else :providers="providers" />
  </div>
</template>

<script>
import OAuth from "./OAuth.vue";

export default {
  components: {
    OAuth
  },
  props: {
    providers: {
      type: Array,
      default: () => []
    },
    error: {
      type: String,
      default: null
    },
    autoRedirect: Boolean
  },
  emits: ["error"],
  data() {
    return {
      redirecting: false
    };
  },
  mounted() {
    // show the error from the OAuth callback in the login view
    if (this.error) {
      this.$emit("error", { message: this.error });
      return;
    }

    if (this.autoRedirect && this.providers.length === 1) {
      this.redirecting = true;
      window.location.href = this.providers[0].href;
    }
  }
};
</script>
