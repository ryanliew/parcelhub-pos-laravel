<template>
	<div class="branch-selector inset-shadow" :class="branchSelectorClass">
        <div class="container">
			<div class="form-inline">
				<template v-if="!isImpersonating">
					<label for="users-selector" class="mr-1">Login as:</label>
					<select id="user-selector" class="custom-select" v-model="current_user" @changed="userChanged">
						<option v-for="user in selectableUsers" :value="user.id" v-if="user">{{ user.name }}</option>
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

				<button type="button" class="btn btn-rounded expand-button" :class="expandButtonClass" @click="toggleExpand" v-html="expandButtonContent"></button>
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
				selectableUsers: [],
				terminals: [],
				isImpersonating: false,
				active: false
			};
		},

		mounted() {
			this.getUsers();
			this.setCurrentBranch();

			this.getStatus();
		},

		methods: {
			getStatus() {
				let memory = this.getCookie("branch-selector");

				this.active = memory !== "close";
			},

			getUsers() {
				let params = this.users;
				if(!params) params = this.userid;

				axios.get("/impersonate/users?allowed=" + params)
					.then(response => this.setUsers(response))
					.catch(error => this.getUsers());
			},

			getCookie(name) {
			  	var value = "; " + document.cookie;
			  	var parts = value.split("; " + name + "=");
			  	if (parts.length == 2) return parts.pop().split(";").shift();
			},

			setUsers(response) {
				if(response.data)
					this.selectableUsers = response.data;
			},

			userChanged(response) {
				axios.get("/impersonate/user?user=" + this.current_user);

				flash("User changed, reloading");

				setTimeout(function(){
					location.reload();
				}, 2000);
			},

			branchChanged(response) {
				flash("Branch changed, reloading");

				setTimeout(function(){
					location.reload();
				}, 2000);
			},

			terminalChanged() {
				flash("Terminal changed, reloading");

				setTimeout(function(){
					location.reload();
				}, 2000);
			},

			leaveImpersonation() {
				axios.get("/impersonate/leave")
					.then(response => this.userChanged());
			},

			setCurrentBranch() {
				this.current_branch = _.filter(this.branches, function(branch){ return this.current == branch.id; }.bind(this))[0];
				this.terminals = this.current_branch.terminals;
			},

			toggleExpand() {
				this.active = !this.active;

				let d = new Date();

				d.setTime(d.getTime() + (0.5*24*60*60*1000));

				document.cookie = "branch-selector=open; path=/; expires=" + d.toUTCString();

				if(!this.active) {
					document.cookie = "branch-selector=close; path=/; expires=" + d.toUTCString();
				}
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