/**
 * JS soubor pro vkládání nových otázek single.
 */

$('#addForm').ajaxForm({
	beforeSubmit: function() {
		$('.question-alert').css('display', 'none');
		$('#questionLOAD').css('display', 'block');
		$('#addForm *').prop('disabled', true);
	},

	// success identifies the function to invoke when the server response
	// has been received
	success: function(data) {
		$('#addForm *').prop('disabled', false);
		$('.question-alert').css('display', 'none');
		if (data == '1') {
			$('#questionOK').css('display', 'block');
			$('#addForm .notDefault').clearFields();
		}
		else {
			$('#questionFAIL').css('display', 'block');
			$('#questionFAIL pre').html(data);
		}
	}
});