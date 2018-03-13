<template>
    <span>
    <li v-for="(title, index) in titles">
		<label v-if="index == 0 && index == titles.length -1" class="song-label"><t text="Title" /></label>
		<label v-if="!(index == 0 && index == titles.length -1)" class="song-label"><t text="Title" /> {{index + 1}}</label>
		<input class="song-txtinput" type="text" v-model="title.value">
		<select name="lang" ng-model="title.lang">
			<option value="">Unknown</option>
  			<option value="de">German</option>
  			<option value="en">English</option>
  			<option value="fr">French</option>
  			<option value="es">Spanish</option>
		</select>
	    <input  type="radio" name="original" value="false" :checked="title.original">
        <button v-if="!(index == 0 && index == titles.length -1)" @click="remove_title(index)" class="svg action icon-delete"> </button>
	</li>
	<button @click="add_title()" class="svg action icon-add"></button>
    </span>
</template>

<script>

        import L10nView from "./l10n.vue";

		export default {
            components: {
				t: L10nView,
		    },
			props: [
				'value'
			],
			data: function() {
				return {
					titles: this.value
				};
			}
		}
</script>