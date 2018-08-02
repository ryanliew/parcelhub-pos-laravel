<template>
	<div class="branch-selector inset-shadow" :class="branchSelectorClass">
        <div class="container">
			<div class="form-inline">
				<template v-if="!isImpersonating">
					<label for="users-selector" class="mr-1">Login as:</label>
					<select id="user-selector" class="custom-select" v-model="current_user" @changed="userChanged">
						<option v-for="user in users" :value="user.id">{{ user.name }}</option>
					</select>
				</template>
				<template v-else>
					<button type="button" class="btn btn-primary" @click="leaveImpersonation">Back to original user</button>
				</template>
				<label for="branch-selector" class="ml-3 mr-1">Branch:</label>
				<select id="branch-selector" class="custom-select" v-model="current">
					<option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
				</select>
				<label for="terminal-selector" class="mr-1 ml-3">Terminal:</label>
				<select id="terminal-selector" class="custom-select" v-model="current_terminal" @changed="terminalChanged">
					<option v-for="terminal in terminals" :value="terminal.id">{{ terminal.name }}</option>
				</select>

				<button type="button" class="btn btn-rounded expand-button" :class="expandButtonClass" @click="active = !active" v-html="expandButtonContent"></button>
			</div>


		</div>
	</div>
</template>

<script>
	export default {
		props: ['users', 'branches', 'terminal', 'default', 'userid'],
		data() {
			return {
				current: this.default,
				current_user: this.userid,
				current_terminal: this.terminal,
				current_branch: '',
				terminals: [],
				isImpersonating: false,
				active: false
			};
		},

		mounted() {
			this.getIsImpersonating();
			this.setCurrentBranch();
		},

		methods: {
			getIsImpersonating() {
				axios.get("/impersonate/check")
					.then(response => this.setIsImpersonating(response));
			},

			setIsImpersonating(response) {
				console.log(response);
				this.isImpersonating = response.data;
			},

			userChanged(response) {
				flash("User changed, reloading");

				setTimeout(function(){
					location.reload();
				}, 3000);
			},

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

			leaveImpersonation() {
				axios.get("/impersonate/leave")
					.then(response => this.userChanged());
			},

			setCurrentBranch() {
				this.current_branch = _.filter(this.branches, function(branch){ return this.current == branch.id; }.bind(this))[0];
				this.terminals = this.current_branch.terminals;
			}
		},

		computed: {
			branchSelectorClass() {
				return [this.active ? 'show-selector' : 'hide-selector', this.isImpersonating ? "impersonating" : ''];
			},

			expandButtonContent() {
				return this.active ? '<i class="fas fa-angle-up"></i>' : '<i class="fas fa-angle-down"></i>';
			},

			expandButtonClass() {
				return this.isImpersonating ? "impersonating" : "";
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
			},

			current_user(newVal, oldVal) {
				axios.post('/user/' + this.userid + '/user/change', {user: newVal})
					.then(response => this.userChanged(response));
			}
		}	
	}
</script>