import Vue from 'vue'
import App from './App.vue'
import router from './router'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faChevronDown, faChevronRight, faFolder, faMinus, faFile, faCog, faSort} from '@fortawesome/free-solid-svg-icons'

import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

library.add(faChevronRight, faChevronDown, faFolder, faMinus, faFile, faCog, faSort )
Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')