
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

window.events = new Vue();
import VueNoty from 'vuejs-noty';
import VueSweetalert2 from 'vue-sweetalert2';
import VueCollapse from 'vue2-collapse';
import vCascade from 'v-cascade';
import VueQrcodeReader from "vue-qrcode-reader";

Vue.use(VueQrcodeReader);
Vue.use(VueSweetalert2);
Vue.use(VueNoty);
Vue.use(VueCollapse);
Vue.use(vCascade);

window.flash = function(message, level = 'success'){
 	window.events.$emit('flash', {message, level});
};

window.swalalert = function(title, message, level = 'warning', callback){
	window.events.$swal({
		title: title,
		type: level,
		html: message,
		showCancelButton: true
	}).then((result) => {
		if(result.value) {
			callback();
		}
	});
}


require('./filters');
require("./mixins");


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component('flash', require('./components/Flash.vue'));
Vue.component('text-input', require('./components/TextInput.vue'));
Vue.component('textarea-input', require('./components/TextAreaInput.vue'));
Vue.component('selector-input', require('./components/SelectorInput.vue'));
Vue.component('checkbox-input', require('./components/CheckboxInput.vue'));
Vue.component('file-input', require('./components/FileInput.vue'));
Vue.component('modal', require('./components/Modal.vue'));
Vue.component('confirmation', require("./components/Confirmation.vue"));



Vue.component('branches-dialog', require('./components/branches/Dialog.vue'));
Vue.component('branch-selector', require('./components/branches/Selector.vue'));
Vue.component('branch-knowledge-dialog', require('./components/branches/Knowledge.vue'));


Vue.component('vendors-dialog', require('./components/vendors/Dialog.vue'));
// Vue.component('vendors-selector', require('./components/vendors/Selector.vue'));

Vue.component('zones-dialog', require('./components/zones/Dialog.vue'));

Vue.component('users-dialog', require('./components/users/Dialog.vue'));

Vue.component('product-types-dialog', require('./components/product-types/Dialog.vue'));

Vue.component('taxes-dialog', require('./components/taxes/Dialog.vue'));

Vue.component('products-dialog', require('./components/products/Dialog.vue'));
Vue.component('products-importer', require('./components/products/Importer.vue'));

// Vue.component('invoices-create', require('./components/invoices/Form.vue'));
// Vue.component('invoices-create', require('./components/invoices/Form-Fast.vue'));
Vue.component('hexaform', require('./components/invoices/Hexaform.vue'));
Vue.component('cancel-dialog', require("./components/invoices/Cancel-Dialog.vue"));
Vue.component('order', require('./components/invoices/Order.vue'));

Vue.component('customers-dialog', require('./components/customers/Dialog.vue'));
Vue.component('statement-dialog', require('./components/customers/Statement.vue'));

Vue.component('terminals-dialog', require('./components/terminals/Dialog.vue'));

Vue.component('pricing-dialog', require('./components/pricing/Dialog.vue'));

Vue.component('groups-dialog', require('./components/groups/Dialog.vue'));
Vue.component('groups-product-dialog', require('./components/groups/ProductDialog.vue'));

Vue.component('permissions-dialog', require('./components/permissions/Dialog.vue'));

Vue.component('cashup-status', require('./components/cashups/Status.vue'));
Vue.component('cashup-details', require("./components/cashups/Details.vue"));

Vue.component('sales-reports-dialog', require('./components/reports/SalesReportDialog.vue'));

Vue.component('members-dialog', require('./components/members/Dialog.vue'));
Vue.component('members-form', require('./components/members/Form.vue'));
Vue.component('members-page', require('./components/members/Page.vue'));
 
const app = new Vue({
    el: '#app',

    mounted() {
    	window.addEventListener('keyup', function(event){
    		if(event.key == "F9") {
    			window.open("/invoices/create", "_blank");
    		}
    	});
    }


});
