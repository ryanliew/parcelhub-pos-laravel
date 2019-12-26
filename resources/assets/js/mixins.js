Vue.mixin({
  	methods: {
    	catchAjaxError(error) {
    		console.log("Error detected: " + error);
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

		calculateItemTax(item) {
			let tax = 0;

			if(item.is_tax_inclusive) {
				let tax_value = item.tax.percentage ? (Math.round(parseFloat(item.price) / (item.tax.percentage / 100 + 1) * 100) / 100 ) : item.tax;
				tax = item.price - tax_value;
			}
			else {
				let rate = item.tax.percentage ? item.tax.percentage : item.tax_rate;

				tax = Math.round(parseFloat(item.price) * item.tax_rate) / 100;
			}

			return tax * item.unit;
		},

		calculateMemberItemTax(item) {
			let tax = 0;

			if(item.is_tax_inclusive) {
				let tax_value = item.tax.percentage ? (Math.round(parseFloat(item.member_price) / (item.tax.percentage / 100 + 1) * 100) / 100 ) : item.tax;

				tax = item.member_price - tax_value;
			}
			else {
				let rate = item.tax.percentage ? item.tax.percentage : item.tax_rate;
				
				tax = Math.round(parseFloat(item.member_price) * item.tax_rate) / 100;
			}

			return tax * item.unit;
		},
  	}
});