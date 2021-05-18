// add this to support async/await
import 'regenerator-runtime/runtime'
import LoginScreen from "./components/LoginScreen";

panel.plugin("thathoff/oauth", {
  created: async () => {
    const settings = await panel.app.$api.get("oauth/settings")
    if (!settings.enabled) {
      return
    }

    // always route back to panel home when trying to access installation view
    if (panel?.app?.$route?.path === "/installation") {
      panel.app.$go("/");
    }

    // prevent routing to the installation view (e.g. like the login view does)
    panel?.app?.$router?.beforeEach((to, _, next) => {
      if (to.path !== "/installation") next();
    });
  },

  login: LoginScreen
});
