<template>
	<div class="form-inline">
		<label for="branch-selector" class="mr-1">Branch:</label>
		<select id="branch-selector" class="custom-select" v-model="current">
			<option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
		</select>
		<label for="terminal-selector" class="mr-1 ml-3">Terminal:</label>
		<select id="terminal-selector" class="custom-select" v-model="current_terminal" @changed="terminalChanged">
			<option v-for="n in current_branch.terminal_count" :value="n">Drawer {{ n }}</option>
		</select>
	</div>
</template>

<script>
	export default {
		props: ['branches', 'terminal', 'default', 'userid'],
		data() {
			return {
				current: this.default,
				current_terminal: this.terminal,
				current_branch: ''
			};
		},

		mounted() {
			this.setCurrentBranch();
		},

		methods: {
			branchChanged(response) {
				flash("Branch changed, reloading");

				setTimeout(function(){
					location.reload();
				}, 3000);
			},

			terminalChanged() {
				flash("Terminal changed, reloading");

				setTimeout(function(){
					location.reload();
				}, 3000);
			},

			setCurrentBranch() {
				this.current_branch = _.filter(this.branches, function(branch){ return this.current == branch.id; }.bind(this))[0];
			}
		},

		watch: {
			current(newCurrent, oldCurrent) {				
				axios.post('/user/' + this.userid + '/branch/change', {branch: newCurrent})
					.then(response => this.branchChanged(response));
			},

			current_terminal(newVal, oldVal) {
				axios.post('/user/' + this.userid + '/terminal/change', {terminal: newVal})
					.then(response => this.terminalChanged(response));
			}
		}	
	}
</script>