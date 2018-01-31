import Vue from 'vue'
import Router from 'vue-router'
import SongView from '../components/song.vue'
import WelcomeView from '../components/welcome.vue'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'WelcomeView',
      component: WelcomeView
    },
    {
      path: '/song/:id',
      name: 'song',
      component: SongView
    }
  ]
})