Vue.mixin({
  	methods: {
    	catchAjaxError(error) {
    		console.log("Error detected: " + error);
    	},

    	calculateItemTax(item) {
			let tax = 0;

			if(item.is_tax_inclusive) {
				tax = item.price - (Math.round(parseFloat(item.price) / (item.tax.percentage / 100 + 1) * 100) / 100 );
			}
			else {
				tax = Math.round(parseFloat(item.price) * item.tax_rate) / 100;
			}

			return tax * item.unit;
		},

		calculateItemTotalPrice(item) {
			let total = parseFloat(item.price) * item.unit;
			
			if(!item.is_tax_inclusive)
				total += this.calculateItemTax(item);

			return total;
		},

		calculateItemMemberTotalPrice(item) {
			let total = parseFloat(item.member_price) * item.unit;
			
			if(!item.is_tax_inclusive)
				total += this.calculateItemMemberTax(item);

			return total;
		},

		calculateMemberItemTax(item) {
			let tax = 0;

			if(item.is_tax_inclusive) {
				tax = item.member_price - (Math.round(parseFloat(item.member_price) / (item.tax.percentage / 100 + 1) * 100) / 100 );
			}
			else {
				tax = Math.round(parseFloat(item.member_price) * item.tax_rate) / 100;
			}

			return tax * item.unit;
		},
  	}
});