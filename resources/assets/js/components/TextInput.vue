<template>
	<div>
		<div class="form-group" :class="extraClass" v-if="!isHorizontal">
			<label v-if="!hideLabel ":for="name" :class="labelClass">{{ label }} <span v-if="required && editable" class="text-danger">*</span></label>
			<div class="input-group" v-if="editable">
				<input :name="name" 
					:id="name" 
					class="form-control" 
					:type="type || 'text'" 
					:value="value" 
					@input="updateValue($event.target.value)"
					ref="inputvertical"
					:step="step ? step : 0.001"
					:placeholder="placeholder"
					:disabled="disabled"
					@keydown.enter.prevent="$emit('enter')"
					@keydown.tab="$emit('tab')"
					@dblclick="$emit('dblclick')" />
				<div class="input-group-append" v-if="addon">
					<button class="btn btn-secondary" type="button" @click="addonAction">{{ addon }}</button>
				</div>
			</div>
			<p class="input__field input__field--hoshi"
					v-html="value"
					v-else>	
			</p>
			<small :id="name + 'Help'" class="form-text text-muted" v-if="helpText && editable">{{ helpText }}</small>
			<span class="text-danger" v-if="error">{{ error }}</span>
		</div>
		<div class="form-group d-flex align-items-center" :class="extraClass" v-else>
			<label class="text-right" v-if="!hideLabel && editable ":for="name" :class="labelClass">{{ label }} <span v-if="required && editable" class="text-danger">*</span></label>
			<b class="invoice-label text-right" v-else>{{ label }}:</b>
			<div class="input-group" v-if="editable">
				<input :name="name" 
					:id="name" 
					class="form-control" 
					:type="type || 'text'" 
					:value="value" 
					@input="updateValue($event.target.value)"
					ref="inputhorizontal"
					:step="step ? step : 0.001"
					:placeholder="placeholder"
					:disabled="disabled"
					@keyup.enter="$emit('enter')"
					@keydown.tab="$emit('tab')"
					@dblclick="$emit('dblclick')" />
				<div class="input-group-append" v-if="addon">
					<button class="btn btn-secondary" type="button" @click="addonAction">{{ addon }}</button>
				</div>
			</div>
			<span class="input__field input__field--hoshi"
					v-html="value"
					v-else>	
			</span>
			<small :id="name + 'Help'" class="form-text text-muted" v-if="helpText && editable">{{ helpText }}</small>
			<span class="text-danger" v-if="error">{{ error }}</span>
		</div>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: ['defaultValue', 'label', 'required', 'error', 'name', 'type', 'editable', 'focus', 'hideLabel', 'placeholder', 'extraClass', 'addon', 'helpText', 'step', 'disabled', 'isHorizontal'],
		data() {
			return {
				localValue: ''
			};
		},

		mounted() {
			if(this.focus && (this.$refs.inputvertical || this.$refs.inputhorizontal))
			{
				this.triggerFocus();
			}
		},

		methods: {
			updateValue(value) {
				this.localValue = value;
				this.$emit('input', value);
			},

			addonAction() {
				this.$emit('addon');
			},

			triggerFocus() {
				// console.log("Focusing on " + this.name);
				if(this.isHorizontal) {
					this.$nextTick( () => this.$refs.inputhorizontal.focus() );
				}
				else {
					this.$nextTick( () => this.$refs.inputvertical.focus() );
				}
			}
		},

		computed: {
			value() {
				if(this.defaultValue === undefined)
				{
					return this.localValue;
				}

				return this.defaultValue;
			},

			labelClass() {
				return this.editable ? '' : 'label-small';
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