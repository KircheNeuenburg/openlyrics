<template>
	<div id="app-navigation">
		<ul>
			<template v-for="song in songs">
				<li v-on:click="update_active_song(song)" v-bind:class="{ active: song.id == active_song.id }">
					<router-link :to="{name: 'song', params: {id: song.id}}" >
					
						{{ song.title }}
					
                    	<span v-if="song.unsaved">*</span>
						<span class="utils">
                    	<button class="svg action icon-delete"
                        	title="Delete song"
                        	
                        	data-placement="bottom"
                        	ng-click="delete(song.id)">
						</button>
                    	<button class="svg action icon-star"
                        	title="Favorite"
                        	
                        	data-placement="bottom">
                        </button>
                	</span>
                	</router-link>                	
				</li>
			</template>
		</ul>
		<app-settings></app-settings>
	</div>
</template>

<script>
		import { mapState, mapActions } from 'vuex';

		import AppSettingsView from './appsettings.vue';

		export default {
			computed: mapState([
				'accounts',
				'songs',
				'active_song'
			]),
			
			components: {
				'app-settings': AppSettingsView
			},
			watch: {
    			// call again the method if the route changes
    			'$route': 'load_song'
  			},
			created () {
				store.dispatch('load_song_list');
			},
			methods: {
				...mapActions([
        			'update_active_song',
      			]),
				load_song() {
					//store.dispatch('load_song');
					
				}
			}
			
		};
</script>
