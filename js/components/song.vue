<template >
    <div v-if="song !== null">
	<div>
		<div class="song-title">
			<h1 > {{song.properties.titles[0].value  }} </h1>
		</div>
		<div class="song-metadata">
		<p> <t text="Version"/>: {{ song.metadata.version}}</p>
		<p> <t text="Created in"/>: {{ song.metadata.created_in}}</p>
		<p> <t text="Modified in"/>: {{ song.metadata.modified_in}}</p>
		<p> <t text="Modified date"/>: {{ song.metadata.modified_date }}</p>
	</div>
	<button @click="save_song()"><t text="Save" /> </button>
	<button @click="load_song(active_song.id)"><t text="Discard changes" /> </button>
	<div>
		<ul>
			<li v-for="(title, index) in song.properties.titles">
				<label v-if="index == 0 && index == song.properties.titles.length -1" class="song-label"><t text="Title" /></label>
				<label v-if="!(index == 0 && index == song.properties.titles.length -1)" class="song-label"><t text="Title" /> {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="title.value">
				<select name="lang" ng-model="title.lang">
					<option value="">Unknown</option>
  					<option value="de">German</option>
  					<option value="en">English</option>
  					<option value="fr">French</option>
  					<option value="es">Spanish</option>
				</select>
				<input  type="radio" name="original" value="false" :checked="title.original">
				<button v-if="!(index == 0 && index == song.properties.titles.length -1)" @click="remove_title(index)" class="svg action icon-delete"> </button>
			</li>
			<button @click="add_title()" class="svg action icon-add"></button>
			<li v-for="(author, index) in song.properties.authors">
				<label v-if="index == 0 && index == song.properties.authors.length -1" class="song-label"><t text="Author"/> </label>
				<label v-if="!(index == 0 && index == song.properties.authors.length -1)" class="song-label"><t text="Author"/> {{index + 1}}</label>
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
				<button @click="remove_author(index)" class="svg action icon-delete"> </button>
			</li>
			<button @click="add_author()" class="svg action icon-add"></button>
			<li >
				<label class="song-label"><t text="Copyright"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.copyright">
			</li>
			<li >
				<label class="song-label"><t text="CCLI number"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.ccli_number">
			</li>
			<li >
				<label class="song-label"><t text="Released"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.release_date">
			</li>
			<li >
				<label class="song-label"><t text="Transposition"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.transposition">
			</li>
			<li >
				<label class="song-label"><t text="Tempo"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.tempo.value">
				<input class="song-txtinput" type="text" v-model="song.properties.tempo.type">
			</li>
			<li >
				<label class="song-label"><t text="Key"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.key">
			</li>
			<li >
				<label class="song-label"><t text="Variant"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.variant">
			</li>
			<li >
				<label class="song-label"><t text="Publisher"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.publisher">
			</li>
			<li >
				<label class="song-label"><t text="Keywords"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.keywords">
			</li>
			<li >
				<label class="song-label"><t text="Verse order"/> </label>
				<input class="song-txtinput" type="text" v-model="song.properties.verse_order">
			</li>
			<li v-for="(songbook, index) in song.properties.songbooks">
				<label v-if="index == 0 && index == song.properties.songbooks.length -1" class="song-label"><t text="Songbook"/></label>
				<label v-if="!(index == 0 && index == song.properties.songbooks.length -1)" class="song-label"><t text="Songbook"/> {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="songbook.name">
				<input class="song-txtinput" type="text" v-model="songbook.entry">
				<button @click="remove_songbook(index)" class="svg action icon-delete"></button>
			</li>
			<button @click="add_songbook()" class="svg action icon-add"></button>
			<li v-for="(theme, index) in song.properties.themes">
				<label v-if="index == 0 && index == song.properties.themes.length -1" class="song-label"><t text="Theme"/> </label>
				<label v-if="!(index == 0 && index == song.properties.themes.length -1)" class="song-label"><t text="Theme"/> {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="theme.value">		
				<select  name="lang" v-model="theme.lang" >
					<option value="">Unknown</option>
  					<option value="de">German</option>
  					<option value="en">English</option>
  					<option value="fr">French</option>
  					<option value="es">Spanish</option>
					<option value="cs">Czech</option>
				</select>
				<button @click="remove_theme(index)" class="svg action icon-delete"> </button>
			</li>
			<button @click="add_theme()" class="svg action icon-add"></button>
			<li v-for="(comment, index) in song.properties.comments">
				<label v-if="index == 0 && index == song.properties.comments.length -1" class="song-label"><t text="Comment"/> </label>
				<label v-if="!(index == 0 && index == song.properties.comments.length -1)" class="song-label"><t text="Comment"/> {{index + 1}}</label>
				<input class="song-txtinput" type="text" v-model="song.properties.comments[index]">
				<button @click="remove_comment(index)" class="svg action icon-delete"></button>
			</li>
			<button @click="add_comment()" class="svg action icon-add"></button>
			<div v-for="(verse, index) in song.lyrics.verses">
				<li>
				<label class="song-label"><t text="Name"/> </label>
				<input class="song-txtinput" type="text" v-model="verse.name">
				<select name="lang" ng-model="verse.lang">
					<option value="">Unknown</option>
				  	<option value="de">German</option>
				  	<option value="en">English</option>
				  	<option value="fr">French</option>
				  	<option value="es">Spanish</option>
				</select>
				<button v-if="!(index == 0 && index == song.lyrics.verses.length -1)" @click="remove_verse(index)" class="svg action icon-delete"></button>
				</li>
				<li v-for="(line, index) in verse.lines">
					<textarea class="song-textarea"  type="text" v-model="verse.lines[index]"></textarea>
					
				</li>
			</div>
			<button @click="add_verse()" class="svg action icon-add"></button>
			
		</ul>

	</div>
		
	</div>
	</div>
</template>


<script>
	  import { mapActions, mapState } from 'vuex'
	  import L10nView from "./l10n.vue";
  	export default {
		components: {
				t: L10nView
		},
        computed: {
      		...mapState([
				'songs',
				'active_song'
      		]),
      		song () {
        		return this.active_song.openlyrics
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
			'add_songbook',
			'remove_songbook',
			'add_theme',
			'remove_theme',
			'add_comment',
			'remove_comment',
			'add_verse',
			'remove_verse',
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
