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
            this.vue.game = Vue.createApp(HGame).mount('#app');
            this.vue.game.setup(this, gamedatas);

            // Intialize HPlayerBoard for each player
            for (var player_id in gamedatas.players) {
                var id = 'player_panel_vue_' + player_id;
                dojo.place('<div id="' + id + '" class="tailwind"></div>', 'player_board_' + player_id);
                this.vue[player_id] = Vue.createApp(HPlayerPanel).mount('#' + id);
                this.vue[player_id].player = gamedatas.players[player_id];
            }
        },

        onEnteringState: function (stateName, args) {
            this.vue.game.onEnteringState(stateName, args);
        },

        onLeavingState: function (stateName) {
            this.vue.game.onLeavingState(stateName, args);
        },

        onUpdateActionButtons: function (stateName, args) {
            this.vue.game.onUpdateActionButtons(stateName, args);
        }
    });
});