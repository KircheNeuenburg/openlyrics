	<textarea editor songs-timeout-change="save()" name="editor"></textarea>
	<div class="song-meta">
		<span class="song-word-count" ng-if="song.content.length > 0">{{song.content | wordCount}}</span>
		<span class="song-meta-right">
			<button class="icon-fullscreen has-tooltip btn-fullscreen" songs-tooltip ng-click="toggleDistractionFree()"></button>
		</span>
	</div>
