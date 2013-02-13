/**
 * Front-end logic for handling course data.
 */
var course = (function ($) {
	var self = {};

	self.form = null;
	self.answer = '';

	self.strings = {
		answered: 'You answered',
		correct: 'correct',
		incorrect: 'incorrect'
	};

	// Initialize the event handling
	self.init = function (options) {
		self.strings = (options && options.strings) ? options.strings : self.strings;
		$('.item-input-form').submit (self.save_input);
	};

	// Save learner input through the API
	self.save_input = function (evt) {
		var id = $(this).data ('id'),
			answer = $('#input-' + id).val ();

		if (typeof answer === 'undefined') {
			var el = this.elements['input-' + id];
			try {
				answer = el.options[el.selectedIndex].value;
			} catch (e) {
				answer = el.value;
			}
		}

		self.form = this;
		self.answer = answer;

		lemur.api.data_submit (
			{id: id, answer: answer},
			self.input_saved
		);

		return false;
	};

	// Handle API response to saving learner input
	self.input_saved = function (res) {
		console.log (res);
		if (! res.success) {
			// handle error
			alert (res.error);
			return;
		}

		// handle success
		var id = $(self.form).data ('id'),
			question = $(self.form).data ('question'),
			answer = self.answer,
			correct = '';

		if (res.data.correct === 'yes') {
			correct = ' (' + self.strings.correct + ')';
		} else if (res.data.correct === 'no') {
			correct = ' (' + self.strings.incorrect + ')';
		}

		$(self.form).replaceWith (
			'<div class="item-answer">' +
			'<strong class="item-question">' + question + '</strong><br />' +
			self.strings.answered + ': <span class="item-answer">' + answer + '</span>' +
			correct + '</div>'
		);
	};

	return self;
})(jQuery);