'use strict';
var AA;
function resize () {
	var height = $(window).height() - $('.navbar').height();
	$('.home_grid').height(height);
}
jQuery(function() {
	// Cross browsing placeholder
	$('input, textarea').placeholder();

	$('.form-group.edit button').bind('click',function() {
		var input = $(this).siblings('input:text,input:password');
		console.log(input);

		input.attr('disabled',!input.attr('disabled'));

	});

	if ($('.steps.voting').length) {

		var s = { //steps

			total : 12, // NUMERO DE PERGUNTAS
			current : 1,
			votes : new Array(this.total),
			pts : {
				'happy' : 3,
				'neutral' : 2,
				'unhappy' : 1,
				'next' : 'x'
			},

			// content : 

			init  : function() {

				$('.steps .footer .next, .emot a').bind('click',s.vote);
				$('.steps .footer .finish').bind('click',s.finish);
				$('.steps .footer .prev').bind('click',s.prev);
				s.load();

			},

			vote : function() {

				s.votes[s.current-1] = s.pts[this.className];
				s.next();

			},

			load : function(n) {

				n = n || s.current;

				var content = $('.steps_content .step' + n).html();

				$('.step_content').html(content);
				$('.steps .footer .counter').text(s.current + '/' + s.total);


			},

			next : function() {

				if (s.current === s.total) {
					$('.steps .footer .next').hide();
					$('.steps .footer .finish').show();
				} else {
					s.current++;
				}

				$('.steps .footer .prev').show();

				s.load();

			},

			prev : function() {
				s.current--;

				$('.steps .footer .next').show();
				$('.steps .footer .finish').hide();

				if (s.current === 1) {
					$('.steps .footer .prev').hide();
				}

				// if (s.votes) // TODO: Indicar qual a pessoa votou


				s.load();
			},

			finish : function() {

				window.location.href = '../resultados?voting=' + s.votes.join(',');

			}


		};

		s.init();



	}

	if ($('.home_grid').length) {

		$(window).resize(resize);
		resize();

	}



	jQuery(document).ready(function() {
		jQuery('.carousel').carousel({interval:1500});
	});

	if ($('form#comment').length) {

		$('form#comment').bind('submit',function(e,o){

			var form = $(this),
				url = form.attr('action'),
				input = form.children('input:text');

			$.ajax({
				type: "POST",
				url: url,
				data: {
					'comment' : input.val()
				},
				success : function (comment) {

					$('#comment_list')
						.prepend('<div style="display: none;" classo="comment user_comment"> <p class="title">' + comment.comment_content + '</p> <p class="author">' + comment.comment_author + '</p> </div>')
						.children('div:first')
							.fadeIn('slow');

					input.val('');

				},
				dataType: 'json'
			});

			return false;

		});

	}


	
});