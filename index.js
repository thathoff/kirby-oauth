if (window.panel) {
  var oauthBasePath = window.panel.site + "/oauth";
  var url = window.location + "";

  // override to login screen
  if (url.indexOf(window.panel.url) === 0) {
    var settings = null;
    var providersHtml = null;
    var tryCounter = 0;

    var updateForm = function() {
      tryCounter++;

      // override  installation screen to oauth screen
      if (window.location == window.panel.url + "/installation") {
        window.location = oauthBasePath;
      }

      var form = document.getElementsByClassName("k-login-form").item(0);

      if (tryCounter > 10) {
        return;
      }

      if (!form) {
        setTimeout(function () {
          updateForm()
        }, 200);
        return;
      }

      if (!settings.onlyOauth) {
        form.insertAdjacentHTML('beforeend', providersHtml);
        return
      }

      form.innerHTML = providersHtml;
    }

    fetch(oauthBasePath + "/settings")
      .then(response => response.json())
      .then(function (settingsData) {
        settings = settingsData;
        if (!settings.enabled) {
          return;
        }

        fetch(oauthBasePath + "/providers")
          .then(response => response.text())
          .then(function (response) {
            providersHtml = response;
            updateForm();
          })
      });
  }
}
