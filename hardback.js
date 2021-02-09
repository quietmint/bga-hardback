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

            // Intiailize Vue
            let app = Vue.createApp(HGame);
            app.config.globalProperties.game = this;
            app.config.globalProperties.emitter = mitt();
            this.vue = app.mount('#happ');
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
            this.vue.onEnteringState(stateName, args);
        },

        onLeavingState: function (stateName) {
            this.vue.onLeavingState(stateName);
        },

        onUpdateActionButtons: function (stateName, args) {
            this.vue.onUpdateActionButtons(stateName, args);
        },

        onNotify: function (notif) {
            this.vue.onNotify(notif);
        },
    });
});