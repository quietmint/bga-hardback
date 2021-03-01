/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Hardback implementation : © quietmint
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * hardback.js
 *
 * hardback user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
  "dojo",
  "dojo/_base/declare",
  "ebg/core/gamegui",
  "ebg/counter",
], function (dojo, declare) {
  return declare("bgagame.hardback", ebg.core.gamegui, {
    constructor: function () {
      this.vue = null;
    },

    onScreenWidthChange: function () {
      // Remove broken "zoom" property added by BGA framework
      this.gameinterface_zoomFactor = 1;
      dojo.style("page-content", "zoom", "");
      dojo.style("page-title", "zoom", "");
      dojo.style("right-side-first-part", "zoom", "");

      // Hide mobile preference on desktop
      var display = dojo.hasClass("ebd-body", "mobile_version") ? "" : "none";
      document
        .getElementById("preference_control_" + HConstants.PREF_ZOOM)
        .closest(".preference_choice").style.display = display;
      document
        .getElementById("preference_fontrol_" + HConstants.PREF_ZOOM)
        .closest(".preference_choice").style.display = display;
    },

    setup: function () {
      console.log("Game setup", this.gamedatas);

      // Old browser check
      if (typeof Vue === "undefined") {
        var msg = _("Your outdated browser is not supported");
        dojo.style("browser-error", "display", "block");
        $("browser-error").innerHTML =
          '<img src="https://noto-website-2.storage.googleapis.com/emoji/emoji_u1f627.png" alt="Anguished Face">' +
          "<div>" +
          msg +
          "</div>" +
          '<div class="ua" title="User-Agent">' +
          navigator.userAgent +
          "</div>" +
          '<div id="errorAbandon" class="bgabutton bgabutton_blue" onclick="$(\'ingame_menu_abandon\').click();return false">' +
          _("Abandon the game (no penalty)") +
          "</div>";
        return;
      }

      // Intiailize Vue
      var app = Vue.createApp(HGame);
      app.config.globalProperties.game = this;
      app.config.globalProperties.emitter = mitt();
      this.vue = app.mount("#HGame");
      this.vue.gamedatas = this.gamedatas;

      // Setup notifications
      dojo.subscribe("cards", this, "onNotify");
      this.notifqueue.setSynchronous("cards");
      dojo.subscribe("cardsPreview", this, "onNotify");
      this.notifqueue.setSynchronous("cardsPreview");
      dojo.subscribe("invalid", this, "onNotify");
      dojo.subscribe("pause", this, "onNotify");
      this.notifqueue.setSynchronous("pause");
      dojo.subscribe("penny", this, "onNotify");
      this.notifqueue.setSynchronous("penny", 1);
      dojo.subscribe("player", this, "onNotify");
      this.notifqueue.setSynchronous("player", 1);

      // Setup preferences
      this.setupPrefs();

      // Production bug report handler
      dojo.subscribe("loadBug", this, function loadBug(n) {
        function fetchNextUrl() {
          var url = n.args.urls.shift();
          console.log("Fetching URL", url);
          dojo.xhrGet({
            url: url,
            load: function (success) {
              console.log("Success for URL", url, success);
              if (n.args.urls.length > 0) {
                fetchNextUrl();
              } else {
                console.log("Done, reloading page");
                window.location.reload();
              }
            },
          });
        }
        console.log("Notif: load bug", n.args);
        fetchNextUrl();
      });
    },

    setupPrefs: function () {
      // Extract the ID and value from the UI control
      var _this = this;
      function onchange(e) {
        var match = e.target.id.match(/^preference_[cf]ontrol_(\d+)$/);
        if (!match) {
          return;
        }
        var id = +match[1];
        var value = +e.target.value;
        _this.prefs[id].value = value;
        _this.onPrefChange(id, value);
      }

      // Call onPrefChange() when any value changes
      dojo.query(".preference_control").connect("onchange", onchange);

      // Call onPrefChange() now
      dojo.forEach(
        dojo.query("#ingame_menu_content .preference_control"),
        function (el) {
          onchange({ target: el });
        }
      );
    },

    /* @Override */
    format_string_recursive: function (log, args) {
      if (this.vue && log && args && !args.processed) {
        log = this.vue.onFormatString(log, args);
        args.processed = true;
      }
      return this.inherited(arguments);
    },

    onEnteringState: function (stateName, args) {
      this.vue && this.vue.onEnteringState(stateName, args);
    },

    onLeavingState: function (stateName) {
      this.vue && this.vue.onLeavingState(stateName);
    },

    onUpdateActionButtons: function (stateName, args) {
      this.vue && this.vue.onUpdateActionButtons(stateName, args);
    },

    onNotify: function (notif) {
      this.vue && this.vue.onNotify(notif);
    },

    onPrefChange: function (id, value) {
      if (id == HConstants.PREF_ZOOM) {
        this.interface_min_width = value == HConstants.ZOOM_SMALL ? 920 : 750;
        this.default_viewport = "width=" + this.interface_min_width;
        this.onGameUiWidthChange();
      }
      this.vue && this.vue.onPrefChange(id, value);
    },
  });
});
