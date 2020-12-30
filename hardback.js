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
            this.vue = {};
        },

        name: function () {
            return 'hardback';
        },

        setup: function (gamedatas) {
            console.log("Game setup", gamedatas);

            // Intiailize HGame
            let app = Vue.createApp(HGame);
            app.config.globalProperties.game = this;
            this.vue.game = app.mount('#app');
            this.vue.game.gamedatas = gamedatas;

            // Intialize HPlayerBoard for each player
            for (var player_id in gamedatas.players) {
                var id = 'player_panel_vue_' + player_id;
                dojo.place('<div id="' + id + '" class="tailwind"></div>', 'player_board_' + player_id);
                let app = Vue.createApp(HPlayerPanel);
                app.config.globalProperties.game = this;
                this.vue[player_id] = app.mount('#' + id);
                this.vue[player_id].player = gamedatas.players[player_id];
            }

            // Setup notifications
            dojo.subscribe('cards', this, 'onNotifyCards');
            dojo.subscribe('player', this, 'onNotifyPlayer');
        },

        onEnteringState: function (stateName, args) {
            this.vue.game.onEnteringState(stateName, args);
        },

        onLeavingState: function (stateName) {
            this.vue.game.onLeavingState(stateName, args);
        },

        onUpdateActionButtons: function (stateName, args) {
            this.vue.game.onUpdateActionButtons(stateName, args);
        },

        onNotifyCards: function (notif) {
            console.log('notify cards', notif);
            this.vue.game.onNotify(notif);
        },

        onNotifyPlayer: function (notif) {
            console.log('notify player', notif);
            let player = notif.args.player;
            this.vue[player.id].player = player;
        },
    });
});