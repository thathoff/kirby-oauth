<template>
  <k-panel>
    <k-view align="center" class="k-installation-view">
      <!-- installation complete -->
      <k-text v-if="isComplete">
        <k-headline>{{ $t("installation.completed") }}</k-headline>
        <k-link to="/login">
          {{ $t("login") }}
        </k-link>
      </k-text>

      <!-- ready to be installed -->
      <form v-else-if="isReady" @submit.prevent="install">
        <h1 class="k-offscreen">
          {{ $t("installation") }}
        </h1>
        <k-fieldset v-model="user" :fields="fields" :novalidate="true" />
        <k-button :text="$t('install')" type="submit" icon="check" />
      </form>

      <!-- custom oauth screen -->
      <OAuth
        v-else-if="showOauth"
        :providers="providers"
        :error="oauthError"
      />

      <!-- not meeting requirements -->
      <div v-else>

        <k-headline>
          {{ $t("installation.issues.headline") }}
        </k-headline>

        <ul class="k-installation-issues">
          <li v-if="isInstallable === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.disabled')" />
          </li>

          <li v-if="requirements.php === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.php')" />
          </li>

          <li v-if="requirements.server === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.server')" />
          </li>

          <li v-if="requirements.mbstring === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.mbstring')" />
          </li>

          <li v-if="requirements.curl === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.curl')" />
          </li>

          <li v-if="requirements.accounts === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.accounts')" />
          </li>

          <li v-if="requirements.content === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.content')" />
          </li>

          <li v-if="requirements.media === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.media')" />
          </li>

          <li v-if="requirements.sessions === false">
            <k-icon type="alert" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span v-html="$t('installation.issues.sessions')" />
          </li>
        </ul>

        <k-button :text="$t('retry')" icon="refresh" @click="$reload" />
      </div>
    </k-view>
  </k-panel>
</template>

<script>
import OAuth from "./OAuth";

export default {
  props: {
    isInstallable: Boolean,
    isInstalled: Boolean,
    isOk: Boolean,
    requirements: Object,
    translations: Array,
    oauthSettings: Object,
    oauthError: String,
  },
  components: {
    OAuth
  },
  data() {
    return {
      user: {
        name: "",
        email: "",
        language: this.$translation.code,
        password: "",
        role: "admin"
      }
    };
  },
  computed: {
    fields() {
      return {
        email: {
          label: this.$t("email"),
          type: "email",
          link: false,
          autofocus: true,
          required: true
        },
        password: {
          label: this.$t("password"),
          type: "password",
          placeholder: this.$t("password") + " …",
          required: true
        },
        language: {
          label: this.$t("language"),
          type: "select",
          options: this.translations,
          icon: "globe",
          empty: false,
          required: true
        }
      };
    },
    isReady() {
      return this.isOk && this.isInstallable;
    },
    isComplete() {
      return this.isOk && this.isInstalled;
    },
    showOauth() {
      return !this.isInstallable && this.oauthSettings && this.oauthSettings.enabled;
    },
    providers () {
      return Object.values(this.oauthSettings.providers)
    }
  },
  created() {
    this.load();
  },
  methods: {
    async install() {
      try {
        await this.$api.system.install(this.user);
        await this.$reload({
          globals: ["$system", "$translation"]
        });
        this.$store.dispatch("notification/success", this.$t("welcome") + "!");
      } catch (error) {
        this.$store.dispatch("notification/error", error);
      }
    },
    async load() {
      this.oauthSettings = await this.$api.get("oauth/settings");
      this.oauthError = (await this.$api.get("oauth/oauthError")).msg;
    }
  }
};
</script>

<style>
/* Make sure the button icon has no offset in k-items */
.k-installation-view .thathoff-oauth-providers .k-button {
  margin-top: 0;
}
</style>
