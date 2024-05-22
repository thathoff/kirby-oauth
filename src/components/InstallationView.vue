<template>
  <k-panel-outside class="k-installation-view">
    <div class="k-dialog k-installation-dialog">
      <k-dialog-body>
        <!-- installation complete -->
        <k-text v-if="isComplete">
          <k-headline>{{ $t("installation.completed") }}</k-headline>
          <k-link to="/login">
            {{ $t("login") }}
          </k-link>
        </k-text>

        <!-- ready to be installed -->
        <div v-else-if="isReady || showOauth">
          <form v-if="isReady" @submit.prevent="install">
            <h1 class="sr-only">
              {{ $t("installation") }}
            </h1>
            <k-fieldset
              :fields="fields"
              :novalidate="true"
              :value="user"
              @input="user = $event"
            />
            <k-button
              :text="$t('install')"
              icon="check"
              size="lg"
              theme="positive"
              type="submit"
              variant="filled"
            />
          </form>

          <!-- custom oauth screen -->
          <OAuth
            v-if="showOauth"
            :providers="providers"
            :error="oauthError"
          />
        </div>

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

          <k-button
            :text="$t('retry')"
            icon="refresh"
            size="lg"
            theme="positive"
            variant="filled"
            @click="$reload"
          />
        </div>
      </k-dialog-body>
    </div>
  </k-panel-outside>
</template>

<script>
import OAuth from "./OAuth.vue";

export default {
  props: {
    isInstallable: Boolean,
    isInstalled: Boolean,
    isOk: Boolean,
    requirements: Object,
    translations: Array
  },
  components: {
    OAuth
  },
  data() {
    return {
      user: {
        name: "",
        email: "",
        language: this.$panel.translation.code,
        password: "",
        role: "admin",
      },
      oauthSettings: {},
      oauthError: null,
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
          icon: "translate",
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
      return (this.oauthSettings && this.oauthSettings.enabled && this.providers.length);
    },
    providers() {
      if (!this.oauthSettings.providers) {
        return [];
      }
      return Object.values(this.oauthSettings.providers)
    }
  },
  mounted () {
    this.loadOauth();
  },
  methods: {
    async install() {
      try {
        await this.$api.system.install(this.user);
        await this.$reload({
          globals: ["$system", "$translation"]
        });

        this.$panel.notification.success(this.$t("welcome") + "!");
      } catch (error) {
        this.$panel.error(error);
      }
    },
    async loadOauth() {
      this.oauthSettings = await this.$api.get("oauth/settings");
      this.oauthError = (await this.$api.get("oauth/oauthError")).msg;
    }
  }
};
</script>

<style>

</style>
