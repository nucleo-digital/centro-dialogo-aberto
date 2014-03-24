'use strict';
function resize () {
	var height = $(window).height() - $('.navbar').height();
	$('.home_grid').height(height);
}
jQuery(function() {
	// Cross browsing placeholder
	$('input, textarea').placeholder();

	$('.form-group.edit button').bind('click',function() {
		var input = $(this).siblings('input:text,input:password');

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

				if (s.current <= s.total) {
					s.current++;
				}
				s.goToStep();

			},

			prev : function() {
				s.current--;
				s.goToStep();
				
			},

			goToStep : function() {

				var show = ['prev','next','counter','step_content','emot'],
					hide = ['back','conclusion','finish'];

				if (s.current > s.total) {

					show = ['finish','conclusion'];
					hide = ['next','counter','step_content','emot'];

				} else if (s.current === 1) {

					show = ['back'];
					hide = ['prev'];

				}

				$('.' + show.join(',.')).show();
				$('.' + hide.join(',.')).hide();

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



	if ($('.proposta').length) {

		var tabs = $('.content-header .tabs .tab a'),
			width = 100/tabs.length-10;

		$('.carousel').carousel({
			interval: 3000
		});

		$('#gallery2 .control').bind('click',function(){

			var gallery2 = $('#gallery2');

			if (gallery2.hasClass('paused')) {
				
				gallery2
					.carousel('cycle')
					.removeClass('paused');
				
			} else {

				gallery2
					.carousel('pause')
					.addClass('paused');



			}

		});

		$('#gallery2 .play span').bind('click',function(){



		});


		var userComent = {

			open : function() {

				$('.user_comment').slideDown('fast');
				$('.vote .quotes').addClass('selected');
				$('.user_comment input:text').focus();

			},

			close : function() {

				$('.user_comment').slideUp('fast');
				$('.vote .quotes').removeClass('selected');

			},

			toggle : function() {

				if ($('.vote .quotes').hasClass('selected')) {
					userComent.close();
				} else {
					userComent.open();
				}

			}


		};

		$('.vote .quotes').bind('click',userComent.toggle);

		var vote = function() {

			var direction = this.id,
				action = $(this).parent().hasClass('selected') ? 'dislike' : 'like',
				url = $(this).data('url'),
				sibling = $(this).parent().siblings('.up,.down'),
				counter = $(this).children('p');

			$(this)
				.parent()
					.toggleClass('selected');

			if (action === 'like') {
				counter.text(parseInt(counter.text())+1);
			} else {
				counter.text(parseInt(counter.text())-1);
			}

			$.post(url + '/' + direction + '/' + action);

			if (sibling.hasClass('selected')) {

				counter = sibling.find('p');
				counter.text(parseInt(counter.text())-1);
				sibling.removeClass('selected');

				direction = direction === 'up' ? 'down': 'up';

				$.post(url + '/' + direction + '/dislike');

			}

			userComent.open();

		};

		$('#up,#down','.vote').bind('click',vote);
		

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

					$('.no_comments').hide();

					$('.comments .comments_wrapper')
						.prepend('<div style="display: none;" class="comment user_hold"> <p class="comment_hold">Comentário aguardando aprovação</p> <p class="comment_author">' + comment.comment_author + '</p> <p class="comment_time">Agora</p>  <p class="comment_title">' + comment.comment_content + '</p></div>')
						.children('div:first')
							.fadeIn('slow');

					userComent.close();

					input.val('');

				},
				dataType: 'json'
			});

			return false;

		});

	}

	if ($('.edit_points').length) {

		var map,img,points,pts,list,mapTop,mapLeft;

		var PTS = {

			init : function() {

				map = $('.map_zone');
				list = map.data('points');
				img = $('img',map);
				// list = '';
				mapTop = parseInt($('.map_zone').position().top);
				mapLeft = parseInt($('.map_zone').position().left);

				pts = $('.map_zone .point');
				PTS.load();

				map.bind('click',PTS.mapClick);

			},

			load : function() {

				var listArr = list.split('l');
					listArr.shift();

				$(listArr).each(function(i,o){
					PTS.plot(o.split('x'));
				});

			},

			mapClick : function(e) {

				if (e.target.id ===	'map') {
					PTS.includePoint(e);
				} else {
					PTS.removePoint(e);
				}

			},

			includePoint : function(e) {

				var x = e.clientX - mapLeft;
				var y = e.clientY - mapTop;
				var imgW = img.width();
				var imgH = img.height();

				// x = x*100/imgW;
				// y = y*100/imgH;

				x = Math.round(x*100/imgW);
				y = Math.round(y*100/imgH);

				PTS.plot([x,y]);

				list = list + 'l' + x + 'x' + y;

				PTS.updateDb();

			},

			removePoint : function(e) {

				var pt = e.target;
				var coord = $(pt).data('coord');

				list = list.replace('l' + coord,'l0x0');

				PTS.updateDb();

				$(pt).remove();


			},

			plot : function(coord) {

				if (coord[0] == 0 || coord[1] == 0) {
					return;
				}

				$('.points').append('<div class="pt pt_del" data-coord="' + coord.join('x') + '" style="left:' + coord[0] + '%;top:' + coord[1] + '%"></div>');

			},

			update : function() {

				pts = $('.map_zone .point');

			},

            updateDb : function() {

                $.post(window.location.href.replace('edit_','update_').replace('?','') + list);

            }

		};

		PTS.init();

	}

	if ($('.sugestao').length) {

		// var data = data;

		// if (!data) {
		// 	data = {};
		// }

		var tab = $('.tab.selected').attr('id');

		// if (tab === 'minha_sugestao' || tab === 'geral') {

			var S = {

				userPoints : data.usr,

				init : function() {

					if (tab != 'geral') {
						S.setFixed();
					}

					// $('.point','.map_zone .spots').not('.fixed').bind('click',function() {
					$('.point','.map_zone .spots').bind('click',function() {
						if ($(this).hasClass('selected')) {
							S.unselectSpot(this);
						} else {
							S.selectSpot(this);
						}
					});

					$('.point','.points').bind('click',S.choosePoint);
					$('.info .create_my_map').bind('click',S.myMap.create);
					$('.info .finish_my_map').bind('click',S.myMap.finish);
					$('.info .edit_my_map').bind('click',S.myMap.edit);
					$('.info .publish_my_map').bind('click',S.myMap.publish);


				},

				setFixed : function() {

					var fixed = $('.fixo');

					fixed.each(function(){

						$('.spots .' + this.id.replace('point_','pt_')).addClass('fixed');

					});

				},

				selectSpot : function(obj) {

					if ($('.tab_minha_sugestao').hasClass('finished')) {
						return;
					}

					$('.point','.map_zone .spots').removeClass('selected');
					$(obj).addClass('selected');

					var id = obj.id.length ? obj.id.replace('pt_','') : null;

					S.selectInfo(id);
					S.selectPoint(id);

				},

				unselectSpot : function(obj) {

					$(obj).removeClass('selected');

					S.unselectPoint();
					S.unselectInfo();

				},

				selectPoint : function(id) {

					if ($('.spots .pt_' + id).hasClass('fixed')) {
						S.unselectPoint();
						return;
					}

					$('.points')
						.removeClass('disabled')
						.addClass('enabled')
						.find('.point')
							.removeClass('selected');

					if (id) {
						$('.points #point_' + id).addClass('selected');
					}

				},

				unselectPoint : function() {

					$('.points')
						.removeClass('enabled')
						.addClass('disabled')
						.find('.point')
							.removeClass('selected');


				},

				selectInfo : function(id) {

					$('.point','.info').removeClass();

					if (id) {
						$('.point_info','.info')
							.html($('.point_wrapper_' + id).html());

						$('.point_info').show();

						if (tab === 'geral') {
							S.selectVoting(id);
						}

					} else {
						$('.point_info').hide();
						$('.voting').hide();
					}


				},

				unselectInfo : function() {

					$('.point_info').hide();
					$('.voting').hide();

				},

				selectVoting : function (id) {

					var index = $('.spots #pt_' + id).data('index')-1,
						total = eval(data.votesCount[index].join('+')),
						length = eval(data.votes[index].length);

					var counts = [
							data.votes[index][0][1]*100/total,
							data.votes[index][1][1]*100/total,
							data.votes[index][2][1]*100/total
							];

					var pts = [
							data.votes[index][0][0],
							data.votes[index][1][0],
							data.votes[index][2][0]
							];

					for (var i=0;i<4;i++)  {


						$('.voting .vote' + (i+1) + ' .pt')
							.removeClass()
								.addClass('pt pt_' + pts[i])
							.parent()
							.siblings()
							.find('span')
							.css('width',counts[i] + '%');

					}

					$('.voting').show();

					
				},

				choosePoint : function() {

					if ($('.points').hasClass('disabled enabled')) {
						return;
					}

					var id = this.id.replace('point_','');

					var spot = $('.point.selected','.map_zone .spots');

					if (spot.hasClass('fixed')) return;


					var index = spot.data('index');
					S.userPoints[index] = id;

					var spotId = spot[0].id;

					spot
						.attr('id','pt_' + id)
						.removeClass(spotId)
						.addClass('pt_' + id);

					S.selectPoint(id);
					S.selectInfo(id);

					$.post(window.location.href.replace('sugestao/minha_sugestao', 'user_points') + S.userPoints.join('l'));

				},

				myMap : {

					create : function() {

					},

					finish : function() {

						$('.point.selected').removeClass('selected');

						$('.tab_minha_sugestao')
							.removeClass('editing')
							.addClass('finished');

					},
					edit : function() {

						$('.tab_minha_sugestao')
							.removeClass('finished')
							.addClass('editing');

					},
					publish : function() {

					}

				}

			};

			S.init();

		// }

	}


	
});