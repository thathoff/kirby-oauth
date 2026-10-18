<template>
  <k-panel-outside class="k-installation-view">
    <h1 class="sr-only">{{ $t("installation") }}</h1>

    <!-- installation complete -->
    <k-stack v-if="isComplete">
      <p>{{ $t("installation.completed") }}.</p>
      <k-button
        :text="$t('login')"
        icon="check"
        link="/login"
        size="lg"
        theme="positive"
        variant="filled"
      />
    </k-stack>

    <!-- ready to be installed -->
    <k-stack v-else-if="isReady || showOauth" gap="var(--spacing-12)">
      <form v-if="isReady" class="k-stack" @submit.prevent="install">
        <k-fieldset :fields="fields" :value="user" @input="user = $event" />
        <k-button
          :text="$t('install')"
          icon="check"
          size="lg"
          theme="positive"
          type="submit"
          variant="filled"
        />
      </form>

      <!-- OAuth providers -->
      <OAuth v-if="showOauth" :providers="oauth.providers" :error="oauth.error" />
    </k-stack>

    <!-- not meeting requirements -->
    <k-stack v-else>
      <k-stack gap="var(--spacing-3)">
        <k-headline>{{ $t("installation.issues.headline") }}</k-headline>
        <k-checklist theme="negative" class="k-installation-issues">
          <li v-for="issue in issues" :key="issue">
            <span v-safe-html="issue" />
          </li>
        </k-checklist>
      </k-stack>
      <k-button
        :text="$t('retry')"
        icon="refresh"
        size="lg"
        theme="positive"
        variant="filled"
        @click="$panel.reload"
      />
    </k-stack>
  </k-panel-outside>
</template>

<script>
import OAuth from "./OAuth.vue";

/**
 * Extends Kirby's installation view to offer OAuth
 * as an alternative to creating the first user manually
 */
export default {
  extends: "k-installation-view",
  components: {
    OAuth
  },
  data() {
    return {
      oauth: {
        providers: [],
        error: null
      }
    };
  },
  computed: {
    showOauth() {
      return this.oauth.providers.length > 0;
    }
  },
  async mounted() {
    this.oauth = await this.$api.get("oauth/installation");
  }
};
</script>
