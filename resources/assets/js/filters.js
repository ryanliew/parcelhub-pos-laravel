Vue.filter('price', function(value){
	return value.toFixed(2);
});

Vue.filter('date', function(value){
	var moment = require("moment");
	return moment(value).format("L");
});