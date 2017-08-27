	<div class="song-title">
		<h1>{{ song.title | songTitle }} </h1>
	</div>
	<form class="song-form">
		<ul>
			<li ng-repeat="titleElement in song.meta.titles">
				<label class="song-label">Title {{$index + 1}}</label>
				<input class="song-txtinput" type="text" ng-model="titleElement.title">
			</li>
			<li ng-repeat="authorElement in song.meta.authors">
				<label class="song-label">Author {{$index + 1}}</label>
				<input class="song-txtinput" type="text" ng-model="authorElement.author">
			</li>
		</ul>
	</form>
	<p>{{song.content}}</p>
	<p>{{song.meta}}</p>
	<p>{{song.lyrics}}</p>
	<!--<textarea editor songs-timeout-change="save()" name="editor"></textarea>-->
	<div class="song-meta">
		<span class="song-meta-right">
			<button class="icon-fullscreen has-tooltip btn-fullscreen" songs-tooltip ng-click="toggleDistractionFree()"></button>
		</span>
	</div>
