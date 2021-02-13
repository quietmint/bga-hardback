import { createApp } from 'vue'
import mitt from 'mitt'
import HGame from './HGame.vue'
import './index.css'

window.Vue = { createApp };
window.mitt = mitt;
window.HGame = HGame;