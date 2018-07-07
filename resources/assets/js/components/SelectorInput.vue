<template>
	<div>
		<label class="select-label" v-if="!hideLabel">
			<span v-text="label"></span>
			<span v-if="required" class="text-danger">*</span>
		</label>
		<div class="control" :class="canClearCss" v-if="editable">
			<v-select :multiple="multiple" :options="potentialData" :value="this.defaultData" @input="updateValue" :name="name" :placeholder="placeholder" :closeOnSelect="!multiple || true">
			</v-select>
		</div>
		<div v-else>
			{{ defaultData.label }}
		</div>
		<span class="text-danger" v-if="error" v-text="error"></span>
	</div>
</template>

<script>
	import vSelect from 'vue-select';
	export default {
		props: { potentialData: Array, label: String, defaultData: {default: ''}, error: String, name: String, placeholder: String, required: {default: false}, multiple: {default: false}, unclearable: {default: false}, hideLabel: {default: false}, editable: {default: true}},

		components: { vSelect },

		data() {
			return {
				
			};
		},

		methods: {
			updateValue(value) {
				this.$emit('input', value);
			}
		},	

		computed: {
			canClearCss() {
				return this.unclearable ? "unclearable" : "";
			}
		}

	}
</script>