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
		
		songs: [],
        active_song: {},
        active_song_backup: {},
	},
	mutations: {
		set_song_list(state, payload) {
			state.songs = payload;
			
		},
		set_song(state, payload) {
			let song = state.songs.find((song) => song.id === payload.id);
			

            let index = state.songs.indexOf(song);

            if (index !== -1) {
                state.songs[index] = payload;
                // updated active song, if selected
                if(state.songs[index].id === state.active_song.id)
                {
                    state.active_song = state.songs[index]
                }
            }
		},
		add_song (state) {
		    const newSong = {
			    text: 'New song',
			    favorite: false
		    }
		    state.songs.push(newSong)
            state.active_song = newSong
            state.active_song_backup = newSong
		},
		
		edit_song (state, data) {
		    state.active_song.data = data
		},
		
		delete_song (state) {
		    state.songs.$remove(state.active_song)
		    state.active_song = state.song[0]
		},
		
		
		set_active_song (state, song) {
            state.active_song = song
            state.active_song_backup = song
        },
        add_title(state, title) {
            state.active_song.song.properties.titles.push(title)
        },
        remove_title(state,index) {
            if (index > -1) {
                state.active_song.song.properties.titles.splice(index, 1);
            }
        },
        discard_changes(state) {
            state.active_song = state.active_song_backup
        },
	},
	getters: {
		
		get_song_by_id: (state) => (id) => {
		  return state.songs.find((song) => song.id === id)
		}
	},
	actions: {
		load_song_list( {commit}) {
			return new Promise(function(resolve) {
				axios.get('/songs').then(response => 
					{commit('set_song_list', response.data);
					
					}
					)
					
					
				
			});
		},
		load_song( {commit},id) {
			return new Promise(function(resolve) {
				axios.get('/songs/'+id).then(response => 
					{commit('set_song', response.data);
					}
					)
			});
        },
        update_active_song ({ commit }, song) {
            commit('set_active_song', song)
        },
        save_song({commit}) {
            axios.put('/songs/'+ store.state.active_song.id,store.state.active_song).then(response => 
                {commit('set_song', response.data);
                
                }
                )
        },
        add_empty_title({commit}) {
            let tmp_title = {value: '', lang: '', original: false}
            commit('add_title', tmp_title)
        },
        remove_title({commit}, index) {
            commit('remove_title', index)
        },
        discard_changes({commit}) {
            commit('discard_changes')
        }
	}
});
