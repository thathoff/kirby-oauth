// add this to support async/await
import 'regenerator-runtime/runtime'
import LoginScreen from "./components/LoginScreen.vue";
import InstallationView from "./components/InstallationView.vue";

panel.plugin("thathoff/oauth", {
  login: LoginScreen,
  components: {
    'k-installation-view': InstallationView
  }
});
