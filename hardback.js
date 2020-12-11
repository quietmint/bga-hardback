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

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter", "ebg/stock"], function (dojo, declare) {
    return declare("bgagame.hardback", ebg.core.gamegui, {
        constructor: function () { },

        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */

        setup: function (gamedatas) {
            console.log("Game setup", gamedatas);

            // Player boards
            for (var playerId in gamedatas.players) {
                var player = gamedatas.players[playerId];
                dojo.place(this.format_block('jstpl_player_panel', player), 'icon_point_' + playerId, 'after');
            }

            // Player cards
            if (!this.isSpectator) {
                var player = gamedatas.players[this.player_id];
                dojo.place(this.format_block('jstpl_player', player), 'game_play_area');

                this.playerCards = new ebg.stock();
                this.playerCards.create(this, $('cards_' + this.player_id), 155, 200);
                this.playerCards.image_items_per_row = 1;
                this.playerCards.setSelectionMode(1);
                this.playerCards.onItemCreate = dojo.hitch(this, 'setupCard');
                dojo.connect(this.playerCards, 'onChangeSelection', this, 'onSelectPlayerCard');

                for (var cardId in gamedatas.hand) {
                    var card = gamedatas.hand[cardId];
                    this.playerCards.addItemType(cardId, card.weight, g_gamethemeurl + 'img/cardblank.jpg', 1);
                    this.playerCards.addToStockWithId(cardId, cardId);
                }
            }

            // Tableau cards
            dojo.place(this.format_block('jstpl_tableau', {}), 'game_play_area');
            this.tableauCards = new ebg.stock();
            this.tableauCards.create(this, $('cards_tableau'), 155, 200);
            this.tableauCards.image_items_per_row = 1;
            this.tableauCards.setSelectionMode(1);
            this.tableauCards.onItemCreate = dojo.hitch(this, 'setupCard');
            dojo.connect(this.tableauCards, 'onChangeSelection', this, 'onSelectTableauCard');

            for (var cardId in gamedatas.hand) {
                var card = gamedatas.hand[cardId];
                this.tableauCards.addItemType(cardId, 1, g_gamethemeurl + 'img/cardblank.jpg', 1);
            }

            this.setupNotifications();
        },

        setupCard: function (cardDiv, typeId, cardId) {
            dojo.addClass(cardDiv, 'card');
            var card = this.gamedatas.hand[typeId];
            console.log('setupCard', card);
            dojo.place(this.format_block('jstpl_card', card), cardDiv);
            dojo.place(this.format_block('jstpl_card_actions', {}), cardDiv);
        },

        onSelectPlayerCard: function (stockId, cardId) {
            var _this = this;
            var cardDiv = this.playerCards.getItemDivId(cardId);

            // Add to tableau (with 1s slide animation) and notify server
            this.tableauCards.addToStockWithId(cardId, cardId, cardDiv);
            this.ajaxBuildTableau();

            // Hide player card now and destroy after animation
            dojo.style(cardDiv, 'visibility', 'hidden');
            setTimeout(function () {
                _this.playerCards.removeFromStockById(cardId);
            }, 1000);
        },

        onSelectTableauCard: function (stockId, cardId) {
            var _this = this;
            var cardDiv = this.tableauCards.getItemDivId(cardId);

            // Add to player (with 1s slide animation)
            this.playerCards.addToStockWithId(cardId, cardId, cardDiv);

            // Remove from tableau and notify server
            this.tableauCards.removeFromStockById(cardId, null, true);
            this.ajaxBuildTableau();

            // Hide tableau card now and update display after animation
            dojo.style(cardDiv, 'visibility', 'hidden');
            setTimeout(function () {
                _this.tableauCards.updateDisplay();
            }, 1000);
        },

        ajaxBuildTableau: function () {
            var cardIds = this.tableauCards.getAllItems().map(item => item.id).join(',');
            console.log('ajaxBuildTableau cardIds', cardIds);
            this.ajaxcall('/hardback/hardback/buildTableau.html', {
                cardIds: cardIds
            }, this, function (result) {
                console.log('buildTableau success', result);
            }, function (error) {
                console.log('buildTableau error', error);
            });
        },

        ///////////////////////////////////////////////////
        //// Game & client states

        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function (stateName, args) {
            console.log('Entering state: ' + stateName);

            switch (stateName) {

                /* Example:
                
                case 'myGameState':
                
                    // Show some HTML block at this game state
                    dojo.style( 'my_html_block_id', 'display', 'block' );
                    
                    break;
               */


                case 'dummmy':
                    break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function (stateName) {
            console.log('Leaving state: ' + stateName);

            switch (stateName) {

                /* Example:
                
                case 'myGameState':
                
                    // Hide the HTML block we are displaying only during this game state
                    dojo.style( 'my_html_block_id', 'display', 'none' );
                    
                    break;
               */


                case 'dummmy':
                    break;
            }
        },

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function (stateName, args) {
            console.log('onUpdateActionButtons: ' + stateName);

            if (this.isCurrentPlayerActive()) {
                switch (stateName) {
                    /*               
                                     Example:
                     
                                     case 'myGameState':
                                        
                                        // Add 3 action buttons in the action status bar:
                                        
                                        this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                                        this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                                        this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                                        break;
                    */
                }
            }
        },


        ///////////////////////////////////////////////////
        //// Player's action

        ///////////////////////////////////////////////////
        //// Notifications

        setupNotifications: function () {
            console.log('notifications subscriptions setup');

            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );

            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
        },
    });
});
