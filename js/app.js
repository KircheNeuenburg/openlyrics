/**
 * @copyright 2017 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2017 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

import AppContentView from './components/appcontent.vue';
import NavigationView from './components/navigation.vue';
import store from './store';

import router from './router/router'




window.axios = axios;
window.store = store;

axios.defaults.baseURL = '/nextcloud/index.php/apps/openlyrics'

export class App {
	start() {
		Vue.mixin({
			t: str => t('openlyrics', str)
		});

		let appView = new Vue({
			el: "#app",
			router,
			store,
			components: {
				'nc-app-content': AppContentView,
				'nc-app-navigation': NavigationView
			}
			
		});

	}
}
