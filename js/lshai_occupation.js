// require jquery ui dialog ~~~

;(function($) {
var categorys = [
	'计算机/互联网/通信/电子/物理',
	'专业咨询与服务',
	'金融',
	'制造业',
	'法律',
	'医疗与健康',
	'教育与培训',
	'体育/休闲/旅游/娱乐',
	'新闻与媒体',
	'商品贸易',
	'日常消费品/零售',
	'建筑业',
	'公共服务业',
	'政府/公共事业/其他',
	'交通运输',
	'农林牧渔业',
];
var cateoptions = {
	'计算机/互联网/通信/电子/物理' : [
   '空间科技',
   '计算机硬件',
   '计算机软件',
   '计算机网络设备',
   '计算机与网络安全',
   '计算机游戏',
   '互联网',
   '半导体',
   '电信与通讯',
   '纳米技术',
   '信息技术与服务',
   '无线通讯',
	],
	'专业咨询与服务' : [
   '管理咨询',
   '招聘、猎头',
   '人力资源',
   '培训',
   '市场推广与广告',
   '市场调研',
   '公共关系',
   '翻译服务',
   '设备维护',
   '外包服务',
   '会务及活动服务',
	],
	'金融' : [
   '保险',
   '财经服务',
   '房地产',
   '投资',
   '银行',
   '风险投资',
   '私募股权投资',
   '证券',
   '会计',  
	],
	'制造业' : [
   '工艺品',
   '飞行航空与宇航',
   '汽车',
   '化工',
   '电子电气设备',
   '机械',
   '采矿与金属',
   '石油与能源',
   '回收与环境保护',
   '造船',
   '纺织及皮革制造',
   '造纸与木材',
   '铁路',
   '玻璃, 陶瓷与混凝土',
   '包装与集装箱',
   '工业管理',
   '工业自动化', 
	],
	'法律' : [
   '法律援助',
   '法律服务', 
	],
	'医疗与健康' : [
   '生物工程',
   '生物信息',
   '医院与医疗',
   '医药品',
   '医疗设备',
   '兽医',
   '健身',
   '心理健康',
   '中医',  
	],
	'教育与培训' : [
		'教育中介服务',
		'检测，认证',
		'义务教育',
		'高等教育',
		'教育管理',
		'E-Learning',
		'教育研究', 
	],
	'体育/休闲/旅游/娱乐' : [
   '娱乐业',
   '休闲度假与旅游',
   '餐饮业',
   '住宿业',
   '体育运动',
   '食品与饮料',
   '娱乐设备与服务',   
	],
	'新闻与媒体' : [
   '广播',
   '电视',
   '电影',
   '报纸',
   '杂志',
   '出版',
   '写作与编辑',
   '文化艺术',
   '印刷',
   '互联网媒体',
   '媒体设计制作',  
	],
	'商品贸易' : [
   '国内贸易',
   '国际贸易',
	],
	'日常消费品/零售' : [
   '化妆品',
   '服装服饰',
   '体育用品',
   '烟草',
   '超市卖场',
   '食物生产',
   '酒类',
   '消费电子',
   '快速消费品',
   '家具',
   '零售',
   '批发',
   '进出口',
   '奢侈品与珠宝', 
	],
	'建筑业': [
   '建筑工程',
   '建材',
   '建筑设计',
   '土木工程',
   '装饰装潢',    
	],
	'公共服务业' : [
   '环保服务',
   '家政服务',
   '宗教协会',
   '物流与供应链',
   '邮政，包裹，零担货运',
   '运输/汽运/铁路',    
	],
	'政府/公共事业/其他' : [
   '政府',
   '外交',
   '司法机关',
   '行政部门',
   '军事机构',
   '卫生社会福利',
   '水利环境公共设施',
   '地质勘探',
   '水电燃气生产供应',
   '公共安全',
   '社会保障',
   '图书馆及博物馆',
   '信息服务',
   '保安服务',  
	],
	'交通运输' : [
   '仓储',
   '航空运输',
   '水上运输',
   '客运及城市公共交通',
	],
  '农林牧渔业' : [
   '种地',
   '林业',
   '渔业',
   '农场',
   '畜牧',  
  ]
};


	$.fn.removeOccupationSelect = function() {
		var self = this;
		self.removeAttr('readOnly');
		self.unbind('click');
		$('#occupop').dialog('destroy');
		$("#occupop").remove();
	};

	$.fn.installOccupationSelect = function() {
		var self = this;
		$('#occupop').dialog('destroy');
		$("#occupop").remove();
		self.after('<div id="occupop" title="选择您的行业">'
				+ '<ul id="occupopbody" class="b_occ clearfix"></ul>'
				+ '</div>');
//		$.ui.dialog.defaults.bgiframe = true;
		$('#occupop').dialog({ autoOpen: false, width : 683, height: 566, draggable : true, resizable : false});
		
		for (var i in categorys) {
			 
			var content = '<li id="' + i + '">';
			content += '<dl><dt>' + categorys[i] + '</dt>';
			content += '<dd><ul class="clearfix">';
			
			for (var j in cateoptions[categorys[i]]) {
				content += '<li id="opt' + i + '-' + j + '"><a href="#">';
				content += cateoptions[categorys[i]][j];			
				content += '</a></li>';	
			}
				
			content += '</ul></dd>';
			content += '</dl></li>';
			
			$('#occupopbody').append(content);
		}
		self.unbind('click').click(function(){
			$('#occupop').dialog('open');
		});
		$('#occupopbody a').data('hostid', self.attr('id')).click(function(){
			$('#' +$(this).data('hostid')).attr('value', $(this).text()).blur();
			var inp = $('#' + $(this).data('hostid')).get(0);
			$.data(inp.form, 'validator').element(inp);
			$('#occupop').dialog('close');
			return false;
		});
	};
	
	
})(jQuery);
