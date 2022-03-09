<template>
	<div id="app" class="container py-4">
		<Books/>
	</div>
</template>

<script>
import Books from './components/Books.vue'

export default {
	name: 'App',
	components: {
		Books,
	},
}
</script>

<style scoped>

</style>
