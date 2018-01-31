<template>
	<div id="app-content">
		<div id="app-content-wrapper">
			<router-view></router-view>
			
		</div>
	</div>
</template>

<script>
		import WelcomeView from "./welcome.vue";
		import L10nView from "./l10n.vue";

		export default {
			components: {
				welcome: WelcomeView,
				t: L10nView
			}
		}
</script>
