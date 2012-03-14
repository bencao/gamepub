;
(function($) {
	$.fn.citySelect1 = function() {
		var self = this;
		$('#citypop1').dialog('destroy');
		$("#citypop1").remove();
		self.after('<div id="citypop1" title="选择您所在的城市">'
				+ '<div id="citypop1body" class="b_city"></div></div>'
				);
		
		var arrPageSizes = getPageSize();
		var top = arrPageSizes[3]/3;
		var left = arrPageSizes[2]/3;
		
		$('#citypop1').dialog({ autoOpen: false, width: 633, height : 403, position: [left, top], resizable: false });
		$(document).data('citypop1id', self.attr('id'));
		
		// add a notice and a other school option for people who is not a univ stud.
		// init provs
		var provcontent = '<div class="p_prov"><p>省份或直辖市</p><ul class="clearfix">';
		for (var i in GP) { 
			provcontent += '<li><a href="#">';
			provcontent += GP[i];		
			provcontent += '</a></li>';	
		}
		provcontent += '</ul><div class="p_city"></div></div>';
		$('#citypop1body').html(provcontent);
		// register handler for provs option
		
		$('#citypop1 div.p_prov a').click(function(){
			$(document).data('citypop1prov', $(this).text());
			
			if (GC1[$(this).text()].length == 1) {
//				$('#province1').val($(document).data('citypop1prov'));
//				$('#city1').val($(document).data('citypop1prov'));
//				$('#district1').val($(document).data('citypop1prov'));
				$('#' + $(document).data('citypop1id')).attr('value', $(document).data('citypop1prov'));
				var inp = $('#' + $(document).data('citypop1id')).get(0);
				$.data(inp.form, 'validator').element(inp);
				$('#city1pop').dialog('close');
				return false;
			}
			
			// load citys
			var citycontent = '<p>城市或大区</p><ul class="clearfix">';
			for (var j in GC1[$(this).text()]) { 
				citycontent += '<li><a href="#">';
				citycontent += GC1[$(this).text()][j];		
				citycontent += '</a></li>';	
			}
			citycontent += '</ul><div class="p_dist"></div>';
			
			$("#citypop1 div.p_city").html(citycontent);
			
			$('#citypop1 div.p_city a').click(function(){
				$(document).data('citypop1city', $(this).text());
				
				if (GC2[$(document).data('citypop1prov')][$(this).text()].length == 1) {
//					$('#province1').val($(document).data('citypop1prov'));
//					$('#city1').val($(document).data('citypop1city'));
//					$('#district1').val($(document).data('citypop1city'));
					$('#' + $(document).data('citypop1id')).attr('value', 
							$(document).data('citypop1prov') + $(document).data('citypop1city'));
					var inp = $('#' + $(document).data('citypop1id')).get(0);
					$.data(inp.form, 'validator').element(inp);
					$('#citypop1').dialog('close');
					return false;
				}
				
				// load districts
				var distcontent = '<p>区、县</p><ul class="clearfix">';
				for (var k in GC2[$(document).data('citypop1prov')][$(this).text()]) { 
					distcontent += '<li><a href="#">';
					distcontent += GC2[$(document).data('citypop1prov')][$(this).text()][k];
					distcontent += '</a></li>';	
				}
				distcontent += '</ul>';
				$("#citypop1 div.p_dist").html(distcontent);
				
				$('#citypop1 div.p_dist a').click(function(){
//					$('#province1').val($(document).data('citypop1prov'));
//					$('#city1').val($(document).data('citypop1city'));
//					$('#district1').val($(this).text());
					if ($(document).data('citypop1city') == $(this).text()) {
						$('#' + $(document).data('citypop1id')).attr('value', 
							$(document).data('citypop1prov') + $(document).data('citypop1city'));
					} else {
						$('#' + $(document).data('citypop1id')).attr('value', 
							$(document).data('citypop1prov') + $(document).data('citypop1city') + $(this).text());
					}
					var inp = $('#' + $(document).data('citypop1id')).get(0);
					$.data(inp.form, 'validator').element(inp);
					$('#citypop1').dialog('close');
					return false;
				});
				return false;
			});
			return false;
		});
		
		self.unbind('focus').focus(function(){
			$('#citypop1').dialog('open');
		}).unbind('click').click(function(){
			$('#citypop1').dialog('open');
		});
	};
		
	$.fn.citySelect = function() {
		var self = this;
		$('#citypop').dialog('destroy');
		$("#citypop").remove();
		self.after('<div id="citypop" title="选择您所在的城市">'
				+ '<div id="citypopbody" class="b_city"></div></div>'
				);
		
		var arrPageSizes = getPageSize();
		var top = arrPageSizes[3]/3;
		var left = arrPageSizes[2]/3;
		
		$('#citypop').dialog({ autoOpen: false, width: 633, height : 403, position : [left, top], draggable : true, resizable: false });
		$(document).data('citypopid', self.attr('id'));
		
		// add a notice and a other school option for people who is not a univ stud.
		// init provs
		var provcontent = '<div class="p_prov"><p>省份或直辖市</p><ul class="clearfix">';
		for (var i in GP) { 
			provcontent += '<li><a href="#">';
			provcontent += GP[i];		
			provcontent += '</a></li>';	
		}
		provcontent += '</ul><div class="p_city"></div></div>';
		$('#citypopbody').html(provcontent);
		// register handler for provs option
		
		$('#citypop div.p_prov a').click(function(){
			$(document).data('citypopprov', $(this).text());
			
			if (GC1[$(this).text()].length == 1) {
				$('#province').val($(document).data('citypopprov'));
				$('#city').val($(document).data('citypopprov'));
				$('#district').val($(document).data('citypopprov'));
				$('#' + $(document).data('citypopid')).attr('value', $(document).data('citypopprov'));
				var inp = $('#' + $(document).data('citypopid')).get(0);
				$.data(inp.form, 'validator').element(inp);
				$('#citypop').dialog('close');
				return false;
			}
			
			// load citys
			var citycontent = '<p>城市或大区</p><ul class="clearfix">';
			for (var j in GC1[$(this).text()]) { 
				citycontent += '<li><a href="#">';
				citycontent += GC1[$(this).text()][j];		
				citycontent += '</a></li>';	
			}
			citycontent += '</ul><div class="p_dist"></div>';
			
			$("#citypop div.p_city").html(citycontent);
			
			$('#citypop div.p_city a').click(function(){
				$(document).data('citypopcity', $(this).text());
				
				if (GC2[$(document).data('citypopprov')][$(this).text()].length == 1) {
					$('#province').val($(document).data('citypopprov'));
					$('#city').val($(document).data('citypopcity'));
					$('#district').val($(document).data('citypopcity'));
					$('#' + $(document).data('citypopid')).attr('value', 
							$(document).data('citypopprov') + $(document).data('citypopcity'));
					var inp = $('#' + $(document).data('citypopid')).get(0);
					$.data(inp.form, 'validator').element(inp);
					$('#citypop').dialog('close');
					return false;
				}
				
				// load districts
				var distcontent = '<p>区、县</p><ul class="clearfix">';
				for (var k in GC2[$(document).data('citypopprov')][$(this).text()]) { 
					distcontent += '<li><a href="#">';
					distcontent += GC2[$(document).data('citypopprov')][$(this).text()][k];
					distcontent += '</a></li>';	
				}
				distcontent += '</ul><div class="p_dist"></div>';
				$("#citypop div.p_dist").html(distcontent);
				
				$('#citypop div.p_dist a').click(function(){
					$('#province').val($(document).data('citypopprov'));
					$('#city').val($(document).data('citypopcity'));
					$('#district').val($(this).text());
					if ($(document).data('citypopcity') == $(this).text()) {
						$('#' + $(document).data('citypopid')).attr('value', 
							$(document).data('citypopprov') + $(document).data('citypopcity'));
					} else {
						$('#' + $(document).data('citypopid')).attr('value', 
							$(document).data('citypopprov') + $(document).data('citypopcity') + $(this).text());
					}
					var inp = $('#' + $(document).data('citypopid')).get(0);
					$.data(inp.form, 'validator').element(inp);
					$('#citypop').dialog('close');
					return false;
				});
				return false;
			});
			return false;
		});
		
		self.unbind('focus').focus(function(){
			$('#citypop').dialog('open');
		}).unbind('click').click(function(){
			$('#citypop').dialog('open');
		});
	};
	
	/*
	 * <div id='schooldialog'>
	 */
	$.fn.schoolSelect = function() {
		var self = this;
		$('#schoolpop').dialog('destroy');
		$("#schoolpop").remove();
		self.after('<div id="schoolpop" title="选择您的学校">'
				+ '<dl id="schoolpopbody" class="b_sch">'
				+ '<dt></dt>'
				+ '<dd></dd>'
				+ '</dl>'
				+ '</div>'
				);
//		$.ui.dialog.defaults.bgiframe = true;
		$('#schoolpop').dialog({ autoOpen: false, width: 633, height : 423, draggable : true, resizable: false });
		$(document).data('schoolpopid', self.attr('id'));
		
		// add a notice and a other school option for people who is not a univ stud.
		// init provs
		var provcontent = '<p>省份</p><ul class="clearfix">';
		for (var i in GP) { 
			provcontent += '<li><a href="#">';
			provcontent += GP[i];		
			provcontent += '</a></li>';	
		}
		provcontent += '</ul>';
		$('.b_sch dt').append(provcontent);
		// register handler for provs option
		
		$('.b_sch dt a').click(function(){
			
			// load schools
			var schoolcontent = '<dd><p>学校<span><a href="" class="b_schdda button60 silver60">其他学校</a></span></p><ul class="clearfix">';
			for (var j in GSCHOOL[$(this).text()]) { 
				schoolcontent += '<li><a href="#" class="b_schdda">';
				schoolcontent += GSCHOOL[$(this).text()][j];		
				schoolcontent += '</a></li>';	
			}
			schoolcontent += '</ul></dd>';
			$('.b_sch dd').replaceWith(schoolcontent);
			$('.b_sch a.b_schdda').click(function(){
				$('#' + $(document).data('schoolpopid')).attr('value', $(this).text());
				var inp = $('#' + $(document).data('schoolpopid')).get(0);
				$.data(inp.form, 'validator').element(inp);
				$('#schoolpop').dialog('close');
				return false;
			});
			return false;
		});
		
		self.unbind('focus').focus(function(){
			$('#schoolpop').dialog('open');
		}).unbind('click').click(function(){
			$('#schoolpop').dialog('open');
		});
		
		$('.b_sch a.b_schdda').click(function(){
			$('#' + $(document).data('schoolpopid')).attr('value', $(this).text());
			var inp = $('#' + $(document).data('schoolpopid')).get(0);
			$.data(inp.form, 'validator').element(inp);
			$('#schoolpop').dialog('close');
			return false;
		});
	};
})(jQuery);
