<template>
	<div id="app-settings">
		<div id="app-settings-header">
			<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content">
				<t text="Settings"></t>
			</button>
		</div>
		<div id="app-settings-content">
			<button @click="change_path()"><t text="Choose folder"/></button>
		</div>
        </div>
</template>

<script>

		import L10nView from './l10n.vue';
		import { mapState, mapActions } from 'vuex'

		export default {
			components: {
				t: L10nView
			},
			computed: {
      		...mapState([
				'path',
      		]),
		},
			methods: {
				...mapActions([
        			'update_path'
      			]),
				change_path() {
					new Promise(function(resolve) {
						OC.dialogs.filepicker(
						t('openlyrics', 'Select a single folder with OpenLyrics *.xml files'),
						function (folder_path) {
								axios.post('/folder', {
            					    path: folder_path
            					})
            					.then(function (response) {
									if(response.data.success === true) {
										OC.Notification.showTemporary(t('openlyrics','Saved'));
									}
									else{
										OC.Notification.showTemporary(t('openlyrics','Invalid path!'));
									}
            					})
            					.catch(function (error) {
									console.log(error)
            					    OC.Notification.showTemporary(t('openlyrics','Error occured!'));
            					});
							
						},
						false,
						'httpd/unix-directory',
						true
					)});
				},
			}
		}

</script>
