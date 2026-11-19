<template>
  <div class="">
    <p v-if="redirecting">{{ $t("thathoff.oauth.autoLoggingIn") }}</p>
    <k-button
      v-else-if="provider"
      :icon="provider.icon"
      :link="provider.href"
      :text="$t('thathoff.oauth.loginWith', {name: provider.name})"
      class="thathoff-oauth-provider"
      size="lg"
      target="_self"
      variant="filled"
    />
  </div>
</template>

<script>
export default {
  props: {
    provider: {
      type: Object,
      default: null
    },
    error: {
      type: String,
      default: null
    },
    redirect: Boolean
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

    if (this.redirect && this.provider) {
      this.redirecting = true;
      window.location.href = this.provider.href;
    }
  }
};
</script>
