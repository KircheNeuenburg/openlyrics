<template>
	<div>
		<div class="song-title">
			<h1 >{{ song.properties.titles[0].value  }} </h1>
		</div>
		<div class="song-metadata">
		<p> Version: {{ song.metadata.version}}</p>
		<p> Created in: {{ song.metadata.created_in}}</p>
		<p> Modified in: {{ song.metadata.modified_in}}</p>
		<p> Modified Date: {{ song.metadata.modified_date }}</p>
	</div>
	<button @click="save_song()">Save </button>
	<button @click="load_song(active_song.id)">Discard </button>
	<div>
		<ul>
			<li v-for="(title, index) in song.properties.titles">
				<label v-if="index == 0 && index == song.properties.titles.length -1" class="song-label">Title</label>
				<label v-if="!(index == 0 && index == song.properties.titles.length -1)" class="song-label">Title {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="title.value">
				<select name="lang" ng-model="title.lang">
					<option value="">Unknown</option>
  					<option value="de">German</option>
  					<option value="en">English</option>
  					<option value="fr">French</option>
  					<option value="es">Spanish</option>
				</select>
				<input  type="radio" name="original" value="false" :checked="title.original">
				<button @click="remove_title(index)">Remove Title </button>
			</li>
			<button @click="add_empty_title()">Add Title </button>
			<li v-for="(author, index) in song.properties.authors">
				<label v-if="index == 0 && index == song.properties.authors.length -1" class="song-label">Author </label>
				<label v-if="!(index == 0 && index == song.properties.authors.length -1)" class="song-label">Author {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="author.value">
				<select name="lang" v-model="author.type">
					<option value=""></option>
  					<option value="music">Music</option>
  					<option value="translation">Translation</option>
  					<option value="words">Words</option>
				</select>
				
				<select v-if="author.type == 'translation'" name="lang" v-model="author.lang" >
					<option value="">Unknown</option>
  					<option value="de">German</option>
  					<option value="en">English</option>
  					<option value="fr">French</option>
  					<option value="es">Spanish</option>
					<option value="cs">Czech</option>
				</select>
			</li>
			<div v-for="verse in song.lyrics.verses">
				<li>
				<label class="song-label">Name </label>
				<input class="song-txtinput" type="text" v-model="verse.name">
				<select name="lang" ng-model="verse.lang">
					<option value="">Unknown</option>
				  	<option value="de">German</option>
				  	<option value="en">English</option>
				  	<option value="fr">French</option>
				  	<option value="es">Spanish</option>
				</select>
				</li>
				<li v-for="(line, index) in verse.lines">
					<textarea class="song-textarea"  type="text" v-model="verse.lines[index]"></textarea>
					
				</li>
			</div>
		</ul>

	</div>
		
	</div>
</template>


<script>
  	import { mapActions, mapState } from 'vuex'
  	export default {
        computed: {
      		...mapState([
				'songs',
				'active_song'
      		]),
      		song () {
        		return this.active_song.song
			},
			id () {
				return parseInt(this.$route.params.id)
			}
		},
    	methods: {
      		...mapActions([
        	'getAllProducts',
			'addToCart',
			'save_song',
			'add_empty_title',
			'remove_title',
			'discard_changes',
			'load_song'
      		])
		},
		mounted () {
					//store.dispatch('load_song');
					console.log('debug')
		}
  	}
</script>
