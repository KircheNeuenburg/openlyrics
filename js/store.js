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
import {openlyrics} from './openlyrics';

Vue.use(Vuex);



export default new Vuex.Store({
	state: {
		
		songs: [],
        active_song: {},
        active_song_backup: {},
        path: '',
	},
	mutations: {
		set_song_list(state, payload) {
            state.songs = payload;
            state.active_song = state.songs[0]
			
		},
		set_song(state, payload) {
			let song = state.songs.find((song) => song.id === payload.id);
			

            let index = state.songs.indexOf(song);

            if (index !== -1 ) {
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
        },
        add_title(state, title) {
            state.active_song.openlyrics.add_title()
        },
        remove_title(state,index) {
            if (index > -1 && (state.active_song.openlyrics.properties.titles.length > 1)) {
                state.active_song.openlyrics.properties.titles.splice(index, 1);
            }
        },
        add_author(state, author) {
            console.log(state.active_song.properties)
            state.active_song.properties.authors.push(author)
        },
        remove_author(state,index) {
            if (index > -1 ) {
                state.active_song.openlyrics.properties.authors.splice(index, 1);
            }
        },
        add_songbook(state, songbook) {
            state.active_song.openlyrics.properties.songbooks.push(songbook)
        },
        remove_songbook(state,index) {
            if (index > -1 ) {
                state.active_song.openlyrics.properties.songbooks.splice(index, 1);
            }
        },
        add_theme(state, theme) {
            state.active_song.openlyrics.properties.themes.push(theme)
        },
        remove_theme(state,index) {
            if (index > -1 ) {
                state.active_song.openlyrics.properties.themes.splice(index, 1);
            }
        },
        add_comment(state, comment) {
            state.active_song.openlyrics.properties.comments.push(comment)
        },
        remove_comment(state,index) {
            if (index > -1 ) {
                state.active_song.openlyrics.properties.comments.splice(index, 1);
            }
        },
        add_verse(state, verse) {
            state.active_song.openlyrics.lyrics.verses.push(verse)
        },
        remove_verse(state,index) {
            if (index > -1 && (state.active_song.openlyrics.lyrics.verses.length > 1)) {
                state.active_song.openlyrics.lyrics.verses.splice(index, 1);
            }
        },
        discard_changes(state) {
            state.active_song = state.active_song_backup
        },
        set_path(state,path) {
            state.path = path
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
					{
                        commit('set_song_list', response.data);
					
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
            console.log("save song")
            axios.put('/songs/'+ store.state.active_song.id,store.state.active_song).then(response => 
                {commit('set_song', response.data);
                
                }
                )
        },
        update_path({commit},folder_path) {
            axios.post('/folder', {
                path: folder_path
            })
            .then(function (response) {
                OC.Notification.showTemporary(t('openlyrics','saved'));
                commit('set_path',folder_path)
            })
            .catch(function (error) {
                OC.Notification.showTemporary(t('openlyrics','Invalid path!'));
            });
        },
        add_title({commit}) {
            let tmp_title = {value: '', lang: '', original: false}
            commit('add_title', tmp_title)
        },
        remove_title({commit}, index) {
            commit('remove_title', index)
        },
        add_author({commit}) {
            let tmp_author = {value: '', lang: '', type: ''}
            commit('add_author', tmp_author)
        },
        remove_author({commit}, index) {
            commit('remove_author', index)
        },
        add_songbook({commit}) {
            let tmp_songbook = {name: '', entry: ''}
            commit('add_songbook', tmp_songbook)
        },
        remove_songbook({commit}, index) {
            commit('remove_songbook', index)
        },
        add_theme({commit}) {
            let tmp_theme = {value: '', lang: ''}
            commit('add_theme', tmp_theme)
        },
        remove_theme({commit}, index) {
            commit('remove_theme', index)
        },
        add_comment({commit}) {
            let tmp_comment = ''
            commit('add_comment', tmp_comment)
        },
        remove_comment({commit}, index) {
            commit('remove_comment', index)
        },
        add_verse({commit}) {
            let tmp_verse = {name: '', lang: '', translit: '', lines: ['']}
            commit('add_verse', tmp_verse)
        },
        remove_verse({commit}, index) {
            commit('remove_verse', index)
        },
        discard_changes({commit}) {
            commit('discard_changes')
        }
	}
});
