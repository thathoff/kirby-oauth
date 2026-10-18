<template>
  <k-stack class="thathoff-oauth-providers" gap="var(--spacing-2)">
    <k-login-alert v-if="error">{{ error }}</k-login-alert>

    <k-headline>{{ $t("thathoff.oauth.signInWith") }}</k-headline>
    <k-button
      v-for="provider in providers"
      :key="provider.id"
      :icon="provider.icon"
      :link="provider.href"
      :text="provider.name"
      :theme="provider.theme ?? 'positive'"
      class="thathoff-oauth-provider"
      size="lg"
      target="_self"
      variant="filled"
    />
  </k-stack>
</template>

<script>
export default {
  name: "OAuth",
  props: {
    providers: {
      type: Array,
      default: () => []
    },
    error: {
      type: String,
      default: null
    }
  }
};
</script>

<style>
.thathoff-oauth-provider {
  width: 100%;
}
</style>
