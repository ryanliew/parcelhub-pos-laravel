<template>
	<div>
		<div class="form-check">
			<label :for="name" class="form-check-label">
			    <input :id="name" :checked="defaultChecked" @click="updateValue" class="form-check-input" type="checkbox">
			    {{ label }}
			</label>
			<small :id="name + 'Help'" class="form-text text-muted" v-if="helpText && editable">{{ helpText }}</small>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['defaultChecked', 'editable', 'label', 'name', 'helpText'],
		data() {
			return {
				
			};
		},

		methods: {
			updateValue(value) {
				this.$emit('input', !this.defaultChecked);
			}
		},

		computed: {
			
		}	
	}
</script>