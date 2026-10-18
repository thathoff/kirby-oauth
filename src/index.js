import LoginOauthMethodForm from "./components/LoginOauthMethodForm.vue";
import InstallationView from "./components/InstallationView.vue";

panel.plugin("thathoff/oauth", {
  components: {
    "k-installation-view": InstallationView,
    "k-login-oauth-method-form": LoginOauthMethodForm
  }
});
