var adminSurvey = {
	moveUp : function(field) {
		var positionElement = $('#id_'+field+'_pos');
		var positionCurrent = parseInt(positionElement.val());
		var currentElement = $('#position-id-'+positionCurrent);
		var nextElement = $('#position-id-'+(positionCurrent-1));

		if (nextElement.size() > 0) {
			currentElement.insertBefore(currentElement.prev());
			positionElement.val(positionCurrent-1);
			currentElement.attr('id','position-id-'+positionElement.val());
			nextElement.find('.pos-attribute').val(positionCurrent);
			nextElement.attr('id','position-id-'+positionCurrent);
		} else {
			console.log('move u');
		}
	},
	moveDown : function(field) {
		var positionElement = $('#id_'+field+'_pos');
		var positionCurrent = parseInt(positionElement.val());
		var currentElement = $('#position-id-'+positionCurrent);
		var nextElement = $('#position-id-'+(positionCurrent+1));

		if (nextElement.size() > 0) {
			currentElement.insertAfter(currentElement.next());
			positionElement.val(positionCurrent+1);
			currentElement.attr('id','position-id-'+positionElement.val());
			nextElement.find('.pos-attribute').val(positionCurrent);
			nextElement.attr('id','position-id-'+positionCurrent);
		}
	},
	addOptionAnswer : function(field) {
		var fieldItem = $('#id_'+field+'_items');	
		fieldItem.focus().val(fieldItem.val()+'\n||==========||\n');
	}
};