	<div class="song-title">
		<h1>{{ song.title | songTitle }} </h1>
	</div>
	<form class="song-form">
		<ul>
			<li>
				<label class="song-label">SongTitle</label>
				<input class="song-txtinput" type="text" ng-model="song.title">
			</li>
			<li>
				<label class="song-label">RealTitle</label>
				<input class="song-txtinput" type="text" ng-model="songjson.song.properties.titles.title">
			</li>
		</ul>
	</form>
	<p>{{songjson}}</p>
	<!--<textarea editor songs-timeout-change="save()" name="editor"></textarea>-->
	<div class="song-meta">
		<span class="song-meta-right">
			<button class="icon-fullscreen has-tooltip btn-fullscreen" songs-tooltip ng-click="toggleDistractionFree()"></button>
		</span>
	</div>
