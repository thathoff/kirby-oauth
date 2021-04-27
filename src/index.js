import LoginScreen from "./components/LoginScreen";

panel.plugin("thathoff/oauth", {
  created: () => {
    // always route back to panel home when trying to access installation view
    if (panel?.app?.$route?.path === "/installation") {
      panel.app.$go("/");
    }

    // prevent routing to the installation view (e.g. like the login view does)
    panel?.app?.$router?.beforeEach((to, _, next) => {
      if (to.path !== "/installation") next();
    });
  },
  use: [
    {
      install: Vue => {
        // NOTE: workaround for using kirby's native login form (k-login-form) without
        //       copying the source code, which cannot be fetched once
        //       window.panel.plugins.login is defined. One solution would be to extend
        //       kirby's native login view, but this is currently not possible due to
        //       the following bug: https://github.com/getkirby/kirby/issues/2346

        // get native kirby login form component
        const kLoginView = Vue.options.components["k-login-view"];
        const kLoginForm = kLoginView.options.components["k-login-form"];

        // patch LoginScreen's kirby login form with kirby native login form
        LoginScreen.components["k-login-form"] = kLoginForm;

        // update kirby native login view to use custom LoginScreen
        Vue.options.components["k-login-view"].options.components = {
          ...Vue.options.components["k-login-view"].options.components,
          "k-login-form": LoginScreen
        };
      }
    }
  ]
});
