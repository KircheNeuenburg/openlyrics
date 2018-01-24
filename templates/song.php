	<div class="song-title">
		<h1 >{{ song.title  }} </h1>
		
	</div>
	<div class="song-metadata">
		<p> Version: {{ song.metadata.version}}</p>
		<p> Created in: {{ song.metadata.created_in}}</p>
		<p> Modified in: {{ song.metadata.modified_in}}</p>
		<p> Modified Date: {{ song.metadata.modified_date | date : 'medium'}}</p>
	</div>
	<form class="song-form">
		<ul>
			<li ng-repeat="title in song.properties.titles">
				<label ng-if="$first && $last" class="song-label">Title</label>
				<label ng-if="!($first && $last)" class="song-label">Title {{$index + 1}}</label>
				<input class="song-txtinput" type="text" ng-model="title.value">
				<select name="lang" ng-model="title.lang">
					<option value="">Unknown</option>
  					<option value="de">German</option>
  					<option value="en">English</option>
  					<option value="fr">French</option>
  					<option value="es">Spanish</option>
				</select>
				<input ng-if="title.original == 'true'" type="radio" name="original" value="false" checked="true">
				<input ng-if="!(title.original == 'true')" type="radio" name="original" value="false">
			</li>
			<li ng-repeat="author in song.properties.authors">
				<label ng-if="$first && $last" class="song-label">Author </label>
				<label ng-if="!($first && $last)" class="song-label">Author {{$index + 1}}</label>
				<input class="song-txtinput" type="text" ng-model="author.value">
				<select name="lang" ng-model="author.type">
					<option value=""></option>
  					<option value="music">Music</option>
  					<option value="translation">Translation</option>
  					<option value="words">Words</option>
				</select>
				
				<select ng-if="author.type == 'translation'" name="lang" ng-model="author.lang" >
					<option value="">Unknown</option>
  					<option value="de">German</option>
  					<option value="en">English</option>
  					<option value="fr">French</option>
  					<option value="es">Spanish</option>
					<option value="cs">Czech</option>
				</select>
			</li>
			<div ng-repeat="verse in song.lyrics">
				<li>
				<label class="song-label">Name </label>
				<input class="song-txtinput" type="text" ng-model="verse.name">
				<select name="lang" ng-model="verse.lang">
					<option value="">Unknown</option>
				  	<option value="de">German</option>
				  	<option value="en">English</option>
				  	<option value="fr">French</option>
				  	<option value="es">Spanish</option>
				</select>
				</li>
				<li ng-repeat="line in verse.lines">
					<textarea class="song-textarea"  ng-model="line"></textarea>
					
				</li>
			</div>
		</ul>

	</form>


	<p>{{song.metadata}}</p>
	<p>{{song.properties}}</p>
	<p>{{song.lyrics}}</p>
	<p><br/></p>
	<p>{{song.content}}</p>
	<p><br/></p>
	<p>{{song.xml_output}}</p>
	<!--<textarea editor songs-timeout-change="save()" name="editor"></textarea>-->
	<div class="song-meta">
		<span class="song-meta-right">
			<button class="icon-fullscreen has-tooltip btn-fullscreen" songs-tooltip ng-click="toggleDistractionFree()"></button>
		</span>
	</div>
