<template>
	<span>{{ translated }}</span>
</template>

<script>
		export default {
			props: [
				'text'
			],
			data: function() {
				return {
					translated: t('openlyrics', this.text)
				};
			}
		}
</script>
