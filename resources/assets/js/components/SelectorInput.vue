<template>
	<div class="mb-3">
		<label class="select-label" v-if="!hideLabel" :class="labelClass">
			<div class="d-flex align-items-center">
				<span v-text="label"></span>
				<span v-if="required && editable" class="text-danger">*</span>
				<span class="fa-stack pointer text-info ml-2" :title="addonTooltip" @click="$emit(addon)" v-if="addon">
					<i class="fas fa-circle fa-stack-2x"></i>
					<i class="fas fa-plus fa-stack-1x fa-inverse text-white"></i>
				</span>
			</div>
		</label>
		<div class="control" :class="canClearCss" v-if="editable">
			<v-select :multiple="multiple" :options="potentialData" :value="this.defaultData" @input="updateValue" :name="name" :placeholder="placeholder" :closeOnSelect="!multiple || true" ref="selector">
			</v-select>
			<input :value="data" :id="name" type="hidden">
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
		props: { potentialData: Array, label: String, defaultData: {default: ''}, error: String, name: String, placeholder: String, required: {default: false}, multiple: {default: false}, unclearable: {default: false}, hideLabel: {default: false}, editable: {default: true}, addon: String, addonTooltip: String},

		components: { vSelect },

		data() {
			return {
				data: ''
			};
		},

		methods: {
			updateValue(value) {
				this.$emit('input', value);
				this.data = value.value;
			}, 

			focus() {
				this.$refs.selector.open = true;
				this.$refs.selector.$refs.search.focus();
			}
		},	

		computed: {
			canClearCss() {
				return this.unclearable ? "unclearable" : "";
			},

			labelClass() {
				return this.editable ? '' : 'label-small';
			}
		}

	}
</script>