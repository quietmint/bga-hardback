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

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter"], function (dojo, declare) {
    return declare("bgagame.hardback", ebg.core.gamegui, {
        constructor: function () {
            this.vue = null;
        },

        name: function () {
            return 'hardback';
        },

        setup: function (gamedatas) {
            console.log("Game setup", gamedatas);

            // Old browser check
            if (typeof Vue === 'undefined') {
                var msg = _('Your outdated browser is not supported');
                dojo.style('browser-error', 'display', 'block');
                $('browser-error').innerHTML = '<img src="https://noto-website-2.storage.googleapis.com/emoji/emoji_u1f627.png" alt="Anguished Face">'
                    + '<div>' + msg + '</div>'
                    + '<div class="ua" title="User-Agent">' + navigator.userAgent + '</div>'
                    + '<div id="errorAbandon" class="bgabutton bgabutton_blue" onclick="$(\'ingame_menu_abandon\').click();return false">' + _('Abandon the game (no penalty)') + '</div>';
                return;
            }

            // Intiailize Vue
            var app = Vue.createApp(HGame);
            app.config.globalProperties.game = this;
            app.config.globalProperties.emitter = mitt();
            this.vue = app.mount('#HGame');
            this.vue.gamedatas = gamedatas;

            // Setup notifications
            dojo.subscribe('cards', this, 'onNotify');
            this.notifqueue.setSynchronous('cards');
            dojo.subscribe('invalid', this, 'onNotify');
            dojo.subscribe('pause', this, 'onNotify');
            this.notifqueue.setSynchronous('pause');
            dojo.subscribe('penny', this, 'onNotify');
            dojo.subscribe('player', this, 'onNotify');
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
    });
});