/**
 * Front-end logic for handling course data.
 */
var course = (function ($) {
	var self = {};

	self.form = null;
	self.answer = '';

	self.strings = {
		answered: 'You have submitting the following answer',
		correct: 'correct',
		incorrect: 'incorrect',
		instructor: 'Sorry, but instructors cannot submit answers.'
	};

	// Initialize the event handling
	self.init = function (options) {
		self.strings = (options && options.strings) ? options.strings : self.strings;
		$('.item-input-form').submit (self.save_input);
		$('.item-reveal h4').click (function () {
			$(this).next ().toggle (400);
			return false;
		});
	};

	// Save learner input through the API
	self.save_input = function (evt) {
		var id = $(this).data ('id'),
			answer = $('#input-' + id).val (),
			instructor = this.elements.hasOwnProperty ('instructor');
		
		if (instructor) {
			alert (self.strings.instructor);
			return false;
		}

		if (typeof answer === 'undefined') {
			var el = this.elements['input-' + id];
			if (typeof el === 'undefined') {
				el = this.elements['input-' + id + '[]'];
				answer = '';
				var sep = '';
				for (var i = 0; i < el.length; i++) {
					if ($(el[i]).is (':checked')) {
						answer += sep + $(el[i]).attr ('value');
						sep = ', ';
					}
				}
			} else if (el.hasOwnProperty ('selectedIndex')) {
				answer = el.options[el.selectedIndex].value;
			} else {
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
			'<div class="learner-response">' +
			'<span class="learner-notice">' + self.strings.answered + ':</span> ' +
			'<span class="learner-answer">' + answer + '</span>' +
			correct + '</div></div>'
		);
	};

	return self;
})(jQuery);