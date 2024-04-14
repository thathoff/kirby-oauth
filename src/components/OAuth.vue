<template>
  <div class="thathoff-oauth-providers">
    <div
      v-if="error !== null"
      class="thathoff-oauth-error"
      data-theme="error"
      @click="error = null"
    >
      <k-icon type="alert" />
      <span>{{ error }}</span>
    </div>

    <k-headline>{{$t('thathoff.oauth.signInWith') }}</k-headline>
    <k-button
      class="thathoff-oauth-provider"
      variant="filled"
      v-for="provider in providers"
      target="_self"
      :icon="provider.icon"
      :link="provider.href"
      :key="provider.id">
      {{  provider.name }}
    </k-button>
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
  }
};
</script>

<style scoped>
.thathoff-oauth-providers {
  padding-top: var(--spacing-10);
  margin-top: var(--spacing-10);
  border-top: 1px solid var(--color-border);
}

.thathoff-oauth-providers:first-child {
  padding-top: 0;
  margin-top: 0;
  border-top: none;
}

/* Fix spacings in install view */
.k-headline {
  margin-top: 0;
  margin-bottom: 0;
  font-weight: var(--font-bold);
}

.thathoff-oauth-error {
  margin-bottom: var(--spacing-5);
  background-color: var(--theme-color-back);
  color: var(--theme-color-text);
  display: flex;
  align-items: center;
  padding: var(--spacing-2);
  border-radius: var(--rounded);
  gap: 0.5rem;
}

.thathoff-oauth-provider {
  width: 100%;
  /* use important to make sure the button does not break in installation view */
  margin-top: var(--spacing-2) !important;
}
</style>
