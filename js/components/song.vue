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
				<button v-if="!(index == 0 && index == song.properties.titles.length -1)" @click="remove_title(index)">Remove Title </button>
			</li>
			<button @click="add_title()">Add Title </button>
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
				<button @click="remove_author(index)">Remove Author </button>
			</li>
			<button @click="add_author()">Add Author</button>
			<li >
				<label class="song-label">Copyright </label>
				<input class="song-txtinput" type="text" v-model="song.properties.copyright">
			</li>
			<li >
				<label class="song-label">CCLI Number </label>
				<input class="song-txtinput" type="text" v-model="song.properties.ccli_number">
			</li>
			<li >
				<label class="song-label">Released </label>
				<input class="song-txtinput" type="text" v-model="song.properties.release_date">
			</li>
			<li >
				<label class="song-label">Transposition </label>
				<input class="song-txtinput" type="text" v-model="song.properties.transposition">
			</li>
			<li >
				<label class="song-label">Tempo </label>
				<input class="song-txtinput" type="text" v-model="song.properties.tempo.value">
				<input class="song-txtinput" type="text" v-model="song.properties.tempo.type">
			</li>
			<li >
				<label class="song-label">Key </label>
				<input class="song-txtinput" type="text" v-model="song.properties.key">
			</li>
			<li >
				<label class="song-label">Variant </label>
				<input class="song-txtinput" type="text" v-model="song.properties.variant">
			</li>
			<li >
				<label class="song-label">Publisher </label>
				<input class="song-txtinput" type="text" v-model="song.properties.publisher">
			</li>
			<li >
				<label class="song-label">Keywords </label>
				<input class="song-txtinput" type="text" v-model="song.properties.keywords">
			</li>
			<li >
				<label class="song-label">Verse Order </label>
				<input class="song-txtinput" type="text" v-model="song.properties.verse_order">
			</li>
			<li >
				<label class="song-label">Transposition </label>
				<input class="song-txtinput" type="text" v-model="song.properties.transposition">
			</li>
			<li v-for="(songbook, index) in song.properties.songbooks">
				<label v-if="index == 0 && index == song.properties.songbooks.length -1" class="song-label">Songbook </label>
				<label v-if="!(index == 0 && index == song.properties.songbooks.length -1)" class="song-label">Songbook {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="songbook.name">
				<input class="song-txtinput" type="text" v-model="songbook.entry">
				<button @click="remove_songbook(index)">Remove Songbook</button>
			</li>
			<button @click="add_songbook()">Add Songbook</button>
			<li v-for="(theme, index) in song.properties.authors">
				<label v-if="index == 0 && index == song.properties.themes.length -1" class="song-label">Theme </label>
				<label v-if="!(index == 0 && index == song.properties.themes.length -1)" class="song-label">Theme {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="theme.value">		
				<select  name="lang" v-model="theme.lang" >
					<option value="">Unknown</option>
  					<option value="de">German</option>
  					<option value="en">English</option>
  					<option value="fr">French</option>
  					<option value="es">Spanish</option>
					<option value="cs">Czech</option>
				</select>
				<button @click="remove_theme(index)">Remove Theme </button>
			</li>
			<button @click="add_theme()">Add Theme</button>
			<li v-for="(comment, index) in song.properties.comments">
				<label v-if="index == 0 && index == song.properties.comments.length -1" class="song-label">Comment </label>
				<label v-if="!(index == 0 && index == song.properties.comments.length -1)" class="song-label">Comment {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="comment.name">
				<input class="song-txtinput" type="text" v-model="comment.entry">
				<button @click="remove_comment(index)">Remove Comment</button>
			</li>
			<button @click="add_comment()">Add Comment</button>
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
			'add_title',
			'remove_title',
			'add_author',
			'remove_author',
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
