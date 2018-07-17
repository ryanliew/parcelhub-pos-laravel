<template>
	<div class="branch-selector inset-shadow" :class="shouldShowClass">
        <div class="container">
			<div class="form-inline">
				<label for="branch-selector" class="mr-1">Branch:</label>
				<select id="branch-selector" class="custom-select" v-model="current">
					<option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
				</select>
				<label for="terminal-selector" class="mr-1 ml-3">Terminal:</label>
				<select id="terminal-selector" class="custom-select" v-model="current_terminal" @changed="terminalChanged">
					<option v-for="terminal in terminals" :value="terminal.id">{{ terminal.name }}</option>
				</select>

				<button type="button" class="btn btn-rounded expand-button" @click="active = !active" v-html="expandButtonContent"></button>
			</div>


		</div>
	</div>
</template>

<script>
	export default {
		props: ['branches', 'terminal', 'default', 'userid'],
		data() {
			return {
				current: this.default,
				current_terminal: this.terminal,
				current_branch: '',
				terminals: [],
				active: false
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
				this.terminals = this.current_branch.terminals;
			},

			toggleActive() {
				console.log("Clicked!");
			}
		},

		computed: {
			shouldShowClass() {
				return this.active ? 'show-selector' : 'hide-selector';
			},

			expandButtonContent() {
				return this.active ? '<i class="fas fa-angle-up"></i>' : '<i class="fas fa-angle-down"></i>';
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