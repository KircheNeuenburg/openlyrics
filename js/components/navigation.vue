<template>
	<div id="app-navigation">
		<ul>
			<template v-for="song in song_list">
				<li>
					<router-link :to="{name: 'song', params: {id: song.id}}">
					
						{{ song.title }}
					
                    	<span v-if="song.unsaved">*</span>
                	</router-link>
                	<span class="utils">
                    	<button class="svg action icon-delete"
                        	title="Delete song"
                        	notes-tooltip
                        	data-placement="bottom"
                        	ng-click="delete(song.id)">
						</button>
                    	<button class="svg action icon-star"
                        	title="Favorite"
                        	notes-tooltip
                        	data-placement="bottom">
                        </button>
                	</span>
				</li>
			</template>
		</ul>
		<app-settings></app-settings>
	</div>
</template>

<script>
		import { mapState } from 'vuex';

		import AppSettingsView from './appsettings.vue';

		export default {
			computed: mapState([
				'accounts',
				'song_list'
			]),
			
			components: {
				'app-settings': AppSettingsView
			},
			
			created () {
					store.dispatch('load_song_list');
				}
			
			
		};
</script>
