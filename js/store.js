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

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		accounts: [],
		song_list: [],
		count: 0
	},
	mutations: {
		setAccounts(state, payload) {
			state.accounts = payload.accounts;
		},
		set_song_list(state, payload) {
			state.song_list = payload;
			
		}
	},
	actions: {
		load_song_list( {commit}) {
			return new Promise(function(resolve) {
				axios.get('/nextcloud/index.php/apps/openlp/songs').then(response => 
					{commit('set_song_list', response.data);
					
					}
					)
					
					
				
			});
		}
	}
});
