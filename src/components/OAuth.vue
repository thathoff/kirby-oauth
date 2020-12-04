<template>
  <div class="thathoff-oauth-providers">
    <div
      v-if="error !== null"
      class="k-login-alert k-login-alert--oauth"
      @click="error = null"
    >
      <span>{{ error }}</span>
      <k-icon type="alert" />
    </div>
    <k-field name="oauth" label="Sign in with">
      <k-list :items="providersForList" />
    </k-field>
  </div>
</template>

<script>
export default {
  name: "OAuth",

  props: {
    providers: {
      type: Array,
      default() {
        return [];
      }
    },
    error: {
      type: String,
      default: null
    }
  },

  computed: {
    providersForList() {
      return this.providers.map(provider => ({
        id: provider.id,
        class: `thathoff-oauth-provider-${provider.id}`,
        text: provider.name,
        link: provider.href,
        target: "_self",
        flag: {
          icon: "check",
          link: provider.href,
          target: "_self"
        }
      }));
    }
  }
};
</script>

<style lang="scss" scoped>
.thathoff-oauth-providers {
  padding-top: 1rem;
}

.k-login-alert {
  &--oauth {
    margin-bottom: 1rem;
  }
}
</style>
