/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Hardback implementation : © quietmint
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 */

define(["dojo", "dojo/_base/declare", "dojo/on", "ebg/core/gamegui", "ebg/counter"], function (dojo, declare, on) {
  // Viewport
  var viewportEl = document.querySelector('meta[name="viewport"]');

  return declare("bgagame.hardback", ebg.core.gamegui, {
    constructor: function () {
      this.vue = null;
      dojo.place("loader_mask", "overall-content", "before");
      this.updateViewport();
    },

    setup: function () {
      console.log("Game setup", this.gamedatas);

      // Unsupported browser check
      if (typeof Vue === "undefined") {
        console.error("Unsupported browser", navigator.userAgent);
        var msg = _("Your outdated browser is not supported");
        dojo.style("browser-error", "display", "block");
        $("browser-error").innerHTML = "<div>" + msg + "</div>" + '<div class="ua" title="User-Agent">' + navigator.userAgent + "</div>" + '<div id="errorAbandon" class="bgabutton bgabutton_blue" onclick="$(\'ingame_menu_abandon\').click();return false">' + _("Abandon the game (no penalty)") + "</div>";
        return;
      }

      // Replay
      if (typeof g_replayFrom !== "undefined") {
        dojo.style("leftright_page_wrapper", "display", "none");
      }

      // #87626: This helps with win/lose message
      this.is_solo = false;

      // Intiailize Vue
      var app = Vue.createApp(HGame);
      app.config.unwrapInjectedRef = true;
      app.config.globalProperties.game = this;
      app.config.globalProperties.emitter = mitt();
      this.vue = app.mount("#HGame");
      this.vue.gamedatas = this.gamedatas;
      this.vue.onSetup();

      // Setup notifications
      dojo.subscribe("cards", this, "onNotify");
      this.notifqueue.setSynchronous("cards");
      dojo.subscribe("cardsPreview", this, "onNotify");
      this.notifqueue.setSynchronous("cardsPreview");
      dojo.subscribe("history", this, "onNotify");
      dojo.subscribe("ink", this, "onNotify");
      dojo.subscribe("invalid", this, "onNotify");
      dojo.subscribe("lookup", this, "onNotify");
      dojo.subscribe("pause", this, "onNotify");
      this.notifqueue.setSynchronous("pause");
      dojo.subscribe("penny", this, "onNotify");
      this.notifqueue.setSynchronous("penny", 1);
      dojo.subscribe("player", this, "onNotify");
      this.notifqueue.setSynchronous("player", 1);
      dojo.subscribe("word", this, "onNotify");

      // Setup preferences
      this.setupPrefs();
    },

    setupPrefs: function () {
      // Convert card size preference to slider
      var cardSizeValue = this.getGameUserPreference(HConstants.PREF_CARD_SIZE);
      var selectTop = document.getElementById("preference_control_" + HConstants.PREF_CARD_SIZE);
      var selectBottom = document.getElementById("preference_fontrol_" + HConstants.PREF_CARD_SIZE);
      selectTop.style.visibility = "hidden";
      selectBottom.style.visibility = "hidden";
      dojo.place('<input id="preference_range_' + HConstants.PREF_CARD_SIZE + '" class="preference_range" type="range" min="1" max="7" value="' + cardSizeValue + '"><small style="float: left">' + _("Small") + '</small><small style="float: right">' + _("Large") + "</small>", selectTop, "before");
      dojo.place('<input id="preference_fange_' + HConstants.PREF_CARD_SIZE + '" class="preference_range" type="range" min="1" max="7" value="' + cardSizeValue + '"><small style="float: left">' + _("Small") + '</small><small style="float: right">' + _("Large") + "</small>", selectBottom, "before");
      dojo.query(".preference_range").connect("onclick", this, function (evt) {
        dojo.stopEvent(evt);
      });
      dojo.query(".preference_range").connect("oninput", this, function (evt) {
        selectTop.value = evt.currentTarget.value;
        on.emit(selectTop, "change", {
          bubbles: false,
          cancelable: false,
        });
      });

      // Call onPrefChange() when dark mode changes
      window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", function (event) {
        this.onGameUserPreferenceChanged(HConstants.PREF_DARK_MODE, this.getGameUserPreference(HConstants.PREF_DARK_MODE));
      });

      this.onGameUserPreferenceChanged(HConstants.PREF_ANIMATION, this.getGameUserPreference(HConstants.PREF_ANIMATION));
      this.onGameUserPreferenceChanged(HConstants.PREF_CARD_SIZE, this.getGameUserPreference(HConstants.PREF_CARD_SIZE));
      this.onGameUserPreferenceChanged(HConstants.PREF_DARK_MODE, this.getGameUserPreference(HConstants.PREF_DARK_MODE));
      this.onGameUserPreferenceChanged(HConstants.PREF_DRAG, this.getGameUserPreference(HConstants.PREF_DRAG));
      this.onGameUserPreferenceChanged(HConstants.PREF_TOOLTIP, this.getGameUserPreference(HConstants.PREF_TOOLTIP));
    },

    /* @Override */
    getRanking: function () {
      this.inherited(arguments);
      this.pageheaderfooter.showSectionFromButton("pageheader_howtoplay");
      this.onShowGameHelp();
    },

    /* @Override */
    onScreenWidthChange: function () {
      this.updateViewport();
    },

    updateViewport: function (chatVisible) {
      if (chatVisible === undefined) {
        chatVisible = false;
        for (var w in this.chatbarWindows) {
          if (this.chatbarWindows[w].status == "expanded") {
            chatVisible = true;
            break;
          }
        }
      }

      // Force mobile view in landscape orientation
      var landscape = window.screen.orientation && window.screen.orientation.type && window.screen.orientation.type.startsWith("landscape");
      var width = chatVisible ? "device-width" : landscape ? 980 : 685;
      this.interface_min_width = width;
      this.default_viewport = "width=" + width + ",interactive-widget=resizes-content";
      viewportEl.content = this.default_viewport;
    },

    /* @Override */
    adaptPlayersPanels: function () {
      if (dojo.hasClass("ebd-body", "mobile_version")) {
        dojo.style("left-side", "marginTop", dojo.position("right-side").h + "px");
      } else {
        dojo.style("left-side", "marginTop", "0px");
      }
    },

    /* @Override */
    showMessage: function (msg, type) {
      if (type == "error" && msg && msg.startsWith("!!!")) {
        return; // suppress red banner and gamelog message
      }
      this.inherited(arguments);
    },

    /* @Override */
    format_string_recursive: function (log, args) {
      if (this.vue && log && args && !args.processed) {
        log = this.vue.onFormatString(log, args);
        args.processed = true;
      }
      return this.inherited(arguments);
    },

    /* @Override */
    onChatKeyDown: function (ev) {
      if (!this.vue.onChatKeyDown(ev)) {
        this.inherited(arguments);
      }
    },

    /* @Override */
    expandChatWindow: function () {
      this.inherited(arguments);
      this.updateViewport(true);
    },

    /* @Override */
    collapseChatWindow: function () {
      this.inherited(arguments);
      this.updateViewport(false);
    },

    /* @Override */
    setLoader: function (percent) {
      if (percent >= 100) {
        document.getElementById("leftright_page_wrapper").style.removeProperty("display");
      }
      this.inherited(arguments);
    },

    /* @Override */
    setModeInstataneous: function () {
      if (!this.instantaneousMode) {
        this.instantaneousMode = true;
        dojo.style("leftright_page_wrapper", "display", "none");
        dojo.style("loader_mask", "display", "block");
        dojo.style("loader_mask", "opacity", 1);
      }
    },

    /* @Override */
    unsetModeInstantaneous: function () {
      if (this.instantaneousMode) {
        this.instantaneousMode = false;
        document.getElementById("leftright_page_wrapper").style.removeProperty("display");
        dojo.style("loader_mask", "display", "none");
      }
    },

    /* @Override */
    onLockInterface: function (lock) {
      if (lock.status == "outgoing" || lock.status == "queued") {
        this.page_title_height = dojo.style("page-title", "height") + "px";
        dojo.style("page-title", "min-height", this.page_title_height);
      } else if (lock.status == "updated" && lock.uuid == this.interface_locked_by_id && this.page_title_height) {
        this.page_title_height = null;
        dojo.style("page-title", "min-height", this.page_title_height);
      }
      this.inherited(arguments);
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

    onGameUserPreferenceChanged(id, value) {
      console.log("Preference changed", id, value);
      if (id == HConstants.PREF_DARK_MODE) {
        var dark = value == HConstants.DARK;
        if (value == HConstants.AUTOMATIC) {
          dark = window.matchMedia("(prefers-color-scheme: dark)").matches;
        }
        dojo.toggleClass(document.documentElement, "dark", dark);
      } else if (id == HConstants.PREF_CARD_SIZE) {
        dojo.removeClass("HGame", "cardsize-1 cardsize-2 cardsize-3 cardsize-4 cardsize-5 cardsize-6 cardsize-7");
        dojo.addClass("HGame", "cardsize-" + value);
      }
      this.vue && this.vue.onPrefChange(id, value);
    },
  });
});
