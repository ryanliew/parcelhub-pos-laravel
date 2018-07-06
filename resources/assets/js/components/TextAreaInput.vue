<template>
	<div class="form-group">
		<div v-if="!hideLabel">
			<label :for="name" v-text="label"></label> <span v-if="required && editable" class="text-danger">*</span>
		</div>
		<textarea class="form-control" :name="name" :id="name" :rows="rows" :cols="cols" :required="required" @input="updateValue($event.target.value)">
		</textarea>
		<span class="text-danger" v-if="error"><strong>{{ error | trans }}</strong></span>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: ['defaultValue', 'label', 'required', 'error', 'name', 'type', 'editable', 'focus', 'hideLabel', 'rows', 'cols'],
		data() {
			return {
				localValue: ''
			};
		},

		mounted() {
			if(this.focus)
			{
				this.$refs.input.focus();
			}
		},

		methods: {
			updateValue(value) {
				this.localValue = value;
				this.$emit('input', value);
			}
		},

		computed: {
			className() {
				if(this.error)
				{
					return 'input__label--hoshi-color-3';
				}
				else if(!this.editable)
				{
					return 'input__label--hoshi-color-transparent';
				}

				return 'input__label--hoshi-color-1';
			},

			inputClass(){
				let theClass = [];
				if( ( this.value !== '' && this.value !== null && this.value ) || this.type == 'date' || !this.editable ) {
					theClass.push('input--filled');
				}

				if(this.hideLabel) {
					theClass.push('input--nolabel');
				}

				return theClass;
			},

			value() {
				if(this.defaultValue === undefined)
				{
					return this.localValue;
				}

				return !this.editable && this.type == 'date' ? moment(this.defaultValue).fromNow() : this.defaultValue;
			}
		},

		filters: {
			ago: function(value) {
				if(!this.editable && this.type == 'date')
					return moment(value).fromNow();
				return value;
			}
		}
	}
</script>